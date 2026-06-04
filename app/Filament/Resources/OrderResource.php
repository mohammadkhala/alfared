<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\DeliveryZone;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'الطلبات';
    protected static ?string $modelLabel = 'طلب';
    protected static ?string $pluralModelLabel = 'الطلبات';
    protected static ?string $navigationGroup = 'المتجر';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('manage_orders') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // ─── العميل ───
            Forms\Components\Section::make('👤 بيانات العميل')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('customer_name')->label('الاسم الكامل')->required()->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')->label('رقم الهاتف')->tel()->required()->maxLength(20),
                        Forms\Components\TextInput::make('customer_email')->label('البريد الإلكتروني')->email()->maxLength(255),
                    ]),
                ])
                ,

            // ─── العنوان ───
            Forms\Components\Section::make('📍 عنوان التوصيل')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('delivery_zone_id')->label('منطقة التوصيل')
                            ->options(DeliveryZone::where('is_active', true)->pluck('name_ar', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(Set $set, Get $get, $state) =>
                                static::recalcTotals($set, $get)),
                        Forms\Components\TextInput::make('city')->label('المدينة')->maxLength(255),
                        Forms\Components\TextInput::make('area')->label('الحي / المنطقة')->maxLength(255),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('address_line')->label('الشارع / تفاصيل العنوان')->maxLength(255),
                        Forms\Components\TextInput::make('building')->label('رقم المبنى / الشقة')->maxLength(255),
                    ]),
                    Forms\Components\Textarea::make('delivery_notes')->label('ملاحظات التوصيل')->rows(2),
                ])
                ,

            // ─── المنتجات ───
            Forms\Components\Section::make('🛍️ منتجات الطلب')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Forms\Components\Grid::make(12)->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('المنتج')
                                    ->options(fn() => Product::query()->where('is_active', true)
                                        ->pluck('name_ar', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->columnSpan(5)
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if ($p = Product::find($state)) {
                                            $set('product_name', $p->name_ar);
                                            $set('price', $p->price);
                                            $set('total', round($p->price * (float)($get('quantity') ?? 1), 2));
                                        }
                                    }),
                                Forms\Components\TextInput::make('quantity')->label('الكمية')
                                    ->numeric()->minValue(1)->default(1)->required()
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->afterStateUpdated(fn(Set $set, Get $get) =>
                                        $set('total', round((float)$get('price') * (float)$get('quantity'), 2))),
                                Forms\Components\TextInput::make('price')->label('السعر (₪)')
                                    ->numeric()->required()->prefix('₪')->columnSpan(2)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(Set $set, Get $get) =>
                                        $set('total', round((float)$get('price') * (float)$get('quantity'), 2))),
                                Forms\Components\TextInput::make('total')->label('الإجمالي')
                                    ->numeric()->prefix('₪')->readOnly()->columnSpan(3),
                            ]),
                            Forms\Components\Hidden::make('product_name'),
                        ])
                        ->addActionLabel('+ إضافة منتج')
                        ->reorderable(false)
                        ->live()
                        ->afterStateUpdated(fn(Set $set, Get $get) => static::recalcTotals($set, $get))
                        ->deleteAction(fn($action) => $action->after(fn(Set $set, Get $get) => static::recalcTotals($set, $get)))
                        ->defaultItems(1)
                        ->columnSpanFull(),
                ])
                ,

            // ─── المبالغ ───
            Forms\Components\Section::make('💰 المبالغ')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('coupon_code')->label('كود الكوبون (اختياري)')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, Get $get) => static::recalcTotals($set, $get)),
                        Forms\Components\Select::make('payment_method')->label('طريقة الدفع')
                            ->options([
                                'cod'      => '💵 دفع عند الاستلام',
                                'lahza'    => '💳 دفع إلكتروني بالبطاقة',
                                'card'     => '💳 بطاقة ائتمان',
                                'transfer' => '🏦 تحويل بنكي',
                            ])
                            ->default('cod')->required(),
                    ]),
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\TextInput::make('subtotal')->label('المجموع الفرعي')
                            ->numeric()->prefix('₪')->readOnly(),
                        Forms\Components\TextInput::make('discount_amount')->label('الخصم')
                            ->numeric()->prefix('₪')->readOnly(),
                        Forms\Components\TextInput::make('delivery_fee')->label('رسوم التوصيل')
                            ->numeric()->prefix('₪')->readOnly(),
                        Forms\Components\TextInput::make('total')->label('الإجمالي النهائي')
                            ->numeric()->prefix('₪')->readOnly()->extraInputAttributes(['style' => 'font-weight:900;font-size:16px;']),
                    ]),
                ])
                ,

            // ─── الحالة (دائما متاحة) ───
            Forms\Components\Section::make('📋 الحالة')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Select::make('status')->label('حالة الطلب')
                        ->options(Order::$statusLabels)
                        ->default('pending')
                        ->required(),
                    Forms\Components\Select::make('payment_status')->label('حالة الدفع')
                        ->options([
                            'pending' => 'في الانتظار',
                            'paid'    => 'مدفوع',
                            'failed'  => 'فشل',
                        ])
                        ->default('pending'),
                ]),
                Forms\Components\Textarea::make('admin_notes')->label('ملاحظات الإدارة')->rows(2),
            ]),
        ]);
    }

    /** Calculate subtotal/discount/delivery/total from current form state. */
    protected static function recalcTotals(Set $set, Get $get): void
    {
        $items = $get('items') ?? [];
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += (float)($item['price'] ?? 0) * (float)($item['quantity'] ?? 0);
        }

        // الخصم من الكوبون
        $discount = 0;
        $code = trim((string)$get('coupon_code'));
        if ($code !== '') {
            $coupon = Coupon::where('code', $code)->where('is_active', true)->first();
            if ($coupon && $subtotal >= (float)($coupon->min_order_amount ?? 0)) {
                $discount = $coupon->type === 'percentage'
                    ? round($subtotal * ($coupon->value / 100), 2)
                    : (float)$coupon->value;
                if ($coupon->max_discount && $discount > $coupon->max_discount) {
                    $discount = (float)$coupon->max_discount;
                }
            }
        }

        // رسوم التوصيل
        $delivery = 0;
        if ($zoneId = $get('delivery_zone_id')) {
            if ($zone = DeliveryZone::find($zoneId)) {
                $delivery = $zone->calculateFee(max(0, $subtotal - $discount));
            }
        }

        $set('subtotal', round($subtotal, 2));
        $set('discount_amount', round($discount, 2));
        $set('delivery_fee', round($delivery, 2));
        $set('total', round(max(0, $subtotal - $discount + $delivery), 2));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('معلومات الطلب')->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('order_number')->label('رقم الطلب')->weight('bold'),
                    Infolists\Components\TextEntry::make('status')->label('الحالة')
                        ->badge()
                        ->color(fn($record) => Order::$statusColors[$record->status] ?? 'gray')
                        ->formatStateUsing(fn($record) => Order::$statusLabels[$record->status] ?? $record->status),
                    Infolists\Components\TextEntry::make('created_at')->label('تاريخ الطلب')->dateTime('d/m/Y h:i A'),
                ]),
            ]),

            Infolists\Components\Section::make('👤 بيانات العميل')->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('customer_name')->label('الاسم'),
                    Infolists\Components\TextEntry::make('customer_phone')->label('الهاتف')->copyable(),
                    Infolists\Components\TextEntry::make('customer_email')->label('البريد'),
                ]),
                Infolists\Components\TextEntry::make('address_line')->label('العنوان'),
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('city')->label('المدينة'),
                    Infolists\Components\TextEntry::make('area')->label('المنطقة'),
                    Infolists\Components\TextEntry::make('building')->label('المبنى'),
                ]),
            ]),

            Infolists\Components\Section::make('🛍️ المنتجات')->schema([
                Infolists\Components\RepeatableEntry::make('items')
                    ->label('')
                    ->schema([
                        Infolists\Components\Grid::make(4)->schema([
                            Infolists\Components\TextEntry::make('product_name')->label('المنتج')->weight('bold'),
                            Infolists\Components\TextEntry::make('quantity')->label('الكمية'),
                            Infolists\Components\TextEntry::make('price')->label('السعر')->money('ILS'),
                            Infolists\Components\TextEntry::make('total')->label('الإجمالي')->money('ILS')->weight('bold'),
                        ]),
                    ]),
            ]),

            Infolists\Components\Section::make('💰 المبالغ')->schema([
                Infolists\Components\Grid::make(4)->schema([
                    Infolists\Components\TextEntry::make('subtotal')->label('المجموع الفرعي')->money('ILS'),
                    Infolists\Components\TextEntry::make('delivery_fee')->label('رسوم الشحن')->money('ILS'),
                    Infolists\Components\TextEntry::make('discount_amount')->label('الخصم')->money('ILS'),
                    Infolists\Components\TextEntry::make('total')->label('الإجمالي')->money('ILS')->weight('bold'),
                ]),
            ]),

            Infolists\Components\Section::make('💳 الدفع')->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('payment_method')->label('طريقة الدفع')
                        ->badge()
                        ->formatStateUsing(fn($state) => match($state) {
                            'lahza'    => '💳 بطاقة',
                            'cod'      => '💵 دفع عند الاستلام',
                            'card'     => '💳 بطاقة ائتمان',
                            'transfer' => '🏦 تحويل بنكي',
                            default    => $state,
                        })
                        ->color(fn($state) => match($state) {
                            'lahza'    => 'success',
                            'cod'      => 'warning',
                            default    => 'primary',
                        }),
                    Infolists\Components\TextEntry::make('payment_status')->label('حالة الدفع')
                        ->badge()
                        ->formatStateUsing(fn($state) => match($state) {
                            'paid'    => '✅ مدفوع',
                            'pending' => '⏳ في الانتظار',
                            'failed'  => '❌ فشل الدفع',
                            default   => $state,
                        })
                        ->color(fn($state) => match($state) {
                            'paid'    => 'success',
                            'pending' => 'warning',
                            'failed'  => 'danger',
                            default   => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('payment_ref')
                        ->label('رقم مرجع لحظة')
                        ->placeholder('—')
                        ->copyable()
                        ->visible(fn($record) => $record->payment_method === 'lahza'),
                ]),
            ]),

            // ─── سجل التعديلات ───
            Infolists\Components\Section::make('📜 سجل التعديلات')
                ->description(fn($record) => $record->lastEditor
                    ? 'آخر تعديل بواسطة ' . $record->lastEditor->name . ' • ' . $record->last_edited_at?->diffForHumans()
                    : 'لا توجد تعديلات بعد')
                ->collapsible()
                ->schema([
                    Infolists\Components\RepeatableEntry::make('audits')
                        ->label('')
                        ->schema([
                            Infolists\Components\Grid::make(12)->schema([
                                Infolists\Components\TextEntry::make('action')
                                    ->label('الإجراء')
                                    ->formatStateUsing(fn($state) => \App\Models\OrderAudit::$actionLabels[$state] ?? $state)
                                    ->columnSpan(3),
                                Infolists\Components\TextEntry::make('user_name')
                                    ->label('بواسطة')
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—')
                                    ->columnSpan(3),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('متى')
                                    ->dateTime('d/m/Y h:i A')
                                    ->columnSpan(3),
                                Infolists\Components\TextEntry::make('changes_summary')
                                    ->label('التغييرات')
                                    ->state(function ($record) {
                                        $state = $record->changes;
                                        if (! is_array($state) || empty($state)) return '—';
                                        $rows = [];
                                        foreach ($state as $field => $diff) {
                                            $before = is_array($diff) ? (string) ($diff['before'] ?? '') : '';
                                            $after  = is_array($diff) ? (string) ($diff['after']  ?? '') : '';
                                            $rows[] = '<strong>' . e($field) . ':</strong> '
                                                . '<span style="color:#94A3B8;text-decoration:line-through;">' . e($before) . '</span>'
                                                . ' → <span style="color:#10B981;font-weight:700;">' . e($after) . '</span>';
                                        }
                                        return new \Illuminate\Support\HtmlString(implode('<br>', $rows));
                                    })
                                    ->html()
                                    ->columnSpan(3),
                            ]),
                        ]),
                ])
                ->visible(fn($record) => $record->audits()->exists()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // ── رقم الطلب + التاريخ ──────────────────────────────
                Tables\Columns\TextColumn::make('order_number')
                    ->label('الطلب')
                    ->searchable()->sortable()
                    ->formatStateUsing(function ($record) {
                        $num  = e($record->order_number);
                        $date = $record->created_at?->format('d/m H:i') ?? '';
                        return new HtmlString(
                            '<div style="line-height:1.5;">'
                            . '<strong style="color:#122870;font-size:13px;">#'.$num.'</strong>'
                            . '<div style="font-size:11px;color:#94A3B8;margin-top:1px;">'.$date.'</div>'
                            . '</div>'
                        );
                    }),

                // ── العميل (اسم + هاتف) ──────────────────────────────
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('العميل')
                    ->searchable()->sortable()
                    ->formatStateUsing(function ($record) {
                        $initial = mb_strtoupper(mb_substr($record->customer_name ?? '؟', 0, 1));
                        $name    = e($record->customer_name);
                        $phone   = e($record->customer_phone);
                        return new HtmlString(
                            '<div style="display:flex;align-items:center;gap:8px;">'
                            . '<div style="width:30px;height:30px;border-radius:50%;background:#E8F0FF;color:#122870;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:12px;flex-shrink:0;">'.$initial.'</div>'
                            . '<div style="min-width:0;">'
                            . '<div style="font-weight:700;font-size:13px;white-space:nowrap;">'.$name.'</div>'
                            . '<div style="font-size:11px;color:#64748B;">'.$phone.'</div>'
                            . '</div></div>'
                        );
                    }),

                // ── المنتجات (أيقونات مضغوطة) ──────────────────────
                Tables\Columns\TextColumn::make('products_preview')
                    ->label('المنتجات')
                    ->state(function ($record) {
                        $count = $record->items()->count();
                        $items = $record->items()->limit(2)->get();
                        $bgs   = ['linear-gradient(135deg,#FFF0E8,#FFE0CC)','linear-gradient(135deg,#E8F0FF,#D0DFFF)'];
                        $html  = '<div style="display:flex;gap:3px;align-items:center;">';
                        foreach ($items as $i => $it) {
                            $html .= '<div style="width:26px;height:26px;border-radius:7px;background:'.$bgs[$i % 2].';display:flex;align-items:center;justify-content:center;font-size:13px;">🛍️</div>';
                        }
                        if ($count > 2) {
                            $html .= '<div style="width:26px;height:26px;border-radius:7px;background:#F1F5F9;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#64748B;">+'..($count-2).'</div>';
                        }
                        $html .= '</div>';
                        return new HtmlString($html);
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                // ── الإجمالي ─────────────────────────────────────────
                Tables\Columns\TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('ILS')->sortable()->weight('bold'),

                // ── الدفع: طريقة + حالة مدمجتان ─────────────────────
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('الدفع')
                    ->formatStateUsing(function ($record) {
                        $method = match($record->payment_method) {
                            'lahza','card' => ['💳 بطاقة', '#D1FAE5', '#065F46'],
                            'cod'          => ['💵 كاش',   '#FEF3C7', '#92400E'],
                            'transfer'     => ['🏦 تحويل', '#DBEAFE', '#1E40AF'],
                            default        => [$record->payment_method, '#F3F4F6', '#374151'],
                        };
                        $status = match($record->payment_status) {
                            'paid'    => ['✅ مدفوع',   '#D1FAE5', '#065F46'],
                            'pending' => ['⏳ بانتظار', '#FEF3C7', '#92400E'],
                            'failed'  => ['❌ فشل',     '#FEE2E2', '#991B1B'],
                            default   => [$record->payment_status, '#F3F4F6', '#374151'],
                        };
                        return new HtmlString(
                            '<div style="display:flex;flex-direction:column;gap:3px;">'
                            . '<span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:'.$method[1].';color:'.$method[2].'">'.$method[0].'</span>'
                            . '<span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:'.$status[1].';color:'.$status[2].'">'.$status[0].'</span>'
                            . '</div>'
                        );
                    }),

                // ── المنطقة (مخفية افتراضياً) ────────────────────────
                Tables\Columns\TextColumn::make('city')
                    ->label('المنطقة')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ── الحالة (select مباشر) ─────────────────────────────
                Tables\Columns\SelectColumn::make('status')
                    ->label('الحالة')
                    ->options(Order::$statusLabels),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')->label('المنطقة')
                    ->options(fn(): array => \App\Models\Order::query()
                        ->whereNotNull('city')
                        ->distinct()
                        ->pluck('city', 'city')
                        ->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('')->tooltip('عرض')
                    ->iconButton()->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()->label('')->tooltip('تعديل')
                    ->iconButton()->icon('heroicon-o-pencil-square'),
                Tables\Actions\Action::make('invoice')
                    ->label('فاتورة')->tooltip('فاتورة')
                    ->iconButton()
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('lang')
                            ->label('لغة الفاتورة')
                            ->options([
                                'ar' => '🇸🇦 العربية',
                                'he' => '🇮🇱 עברית',
                                'en' => '🇬🇧 English',
                            ])
                            ->default('ar')
                            ->required(),
                    ])
                    ->action(function (array $data, Order $record) {
                        return redirect()->away(route('orders.invoice', [
                            'order' => $record,
                            'lang'  => $data['lang'],
                        ]));
                    }),
                Tables\Actions\Action::make('whatsapp')
                    ->label('')
                    ->tooltip('واتساب')
                    ->iconButton()
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn(Order $record) =>
                        "https://wa.me/970{$record->customer_phone}?text={$record->whatsapp_message}"
                    )
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('print_invoices')
                        ->label('🖨️ طباعة الفواتير')
                        ->icon('heroicon-o-printer')
                        ->color('warning')
                        ->deselectRecordsAfterCompletion()
                        ->form([
                            Forms\Components\Select::make('lang')
                                ->label('لغة الفاتورة')
                                ->options([
                                    'ar' => '🇸🇦 العربية',
                                    'he' => '🇮🇱 עברית',
                                    'en' => '🇬🇧 English',
                                ])
                                ->default('ar')
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            $ids = $records->pluck('id')->implode(',');
                            return redirect()->away(route('orders.invoices.bulk', [
                                'ids'  => $ids,
                                'lang' => $data['lang'],
                            ]));
                        }),
                    Tables\Actions\BulkAction::make('export_excel')
                        ->label('📥 تصدير Excel')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('info')
                        ->deselectRecordsAfterCompletion()
                        ->action(function ($records) {
                            $ids = $records->pluck('id')->implode(',');
                            return redirect()->away(route('orders.export', ['ids' => $ids]));
                        }),
                    Tables\Actions\BulkAction::make('mark_confirmed')
                        ->label('تأكيد الطلبات')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->action(fn($records) => $records->each->update(['status' => 'confirmed'])),
                    Tables\Actions\BulkAction::make('mark_shipped')
                        ->label('تحديد كـ شُحن')
                        ->icon('heroicon-o-truck')
                        ->action(fn($records) => $records->each->update(['status' => 'shipped'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'danger'; }
}
