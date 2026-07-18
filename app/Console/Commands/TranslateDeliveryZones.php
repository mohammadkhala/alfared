<?php

namespace App\Console\Commands;

use App\Models\DeliveryZone;
use Illuminate\Console\Command;

/**
 * Fills name_en/name_he on the delivery zones.
 *
 * English comes from palestine-locations and the seeder already sets it, but
 * Hebrew exists in no source file — the names below are written by hand. Keyed
 * by name_ar because that's the one field every zone reliably has.
 */
class TranslateDeliveryZones extends Command
{
    protected $signature = 'zones:translate {--force : أعد الكتابة فوق الترجمات الموجودة}';

    protected $description = 'تعبئة الأسماء الإنجليزية والعبرية لمناطق التوصيل';

    /** name_ar => [en, he] */
    private const NAMES = [
        // ── المناطق الرئيسية ──
        'الضفة الغربية' => ['West Bank', 'הגדה המערבית'],
        'القدس'          => ['Jerusalem', 'ירושלים'],
        'الداخل'         => ['Inside', 'ישראל'],

        // ── الضفة الغربية ──
        'جنين'                => ['Jenin', "ג'נין"],
        'طوباس'               => ['Tubas', 'טובאס'],
        'طولكرم'              => ['Tulkarm', 'טולכרם'],
        'قلقيلية'             => ['Qalqilya', 'קלקיליה'],
        'نابلس'               => ['Nablus', 'שכם'],
        'سلفيت'               => ['Salfit', 'סלפית'],
        'رام الله والبيرة'    => ['Ramallah and Al-Bireh', 'ראם אללה ואל-בירה'],
        'أريحا والأغوار'      => ['Jericho and Al-Aghwar', 'יריחו והבקעה'],
        'بيت لحم'             => ['Bethlehem', 'בית לחם'],
        'الخليل'              => ['Hebron', 'חברון'],

        // ── الداخل ──
        'الناصرة'        => ['Nazareth', 'נצרת'],
        'أم الفحم'       => ['Umm al-Fahm', 'אום אל-פחם'],
        'سخنين'          => ['Sakhnin', "סח'נין"],
        'شفا عمرو'       => ['Shefa-Amr', 'שפרעם'],
        'طمرة'           => ['Tamra', 'טמרה'],
        'رهط'            => ['Rahat', 'רהט'],
        'باقة الغربية'   => ['Baqa al-Gharbiyye', 'באקה אל-גרביה'],
        'الطيبة'         => ['Tayibe', 'טייבה'],
        'الطيرة'         => ['Al Tireh', 'טירה'],
        'كفر قاسم'       => ['Kafr Qasim', 'כפר קאסם'],
        'عكا العربية'    => ['Arab Acre', 'עכו'],
        'حيفا العربية'   => ['Arab Haifa', 'חיפה'],
        'اللد'           => ['Lod', 'לוד'],
        'الرملة'         => ['Ramla', 'רמלה'],
        'يافا'           => ['Jaffa', 'יפו'],
        'دالية الكرمل'   => ['Daliyat al-Karmel', 'דאלית אל-כרמל'],
        'المغار'         => ['Maghar', 'מגאר'],
        'كفر ياسيف'      => ['Kafr Yasif', 'כפר יאסיף'],
        'أبو غوش'        => ['Abu Ghosh', 'אבו גוש'],
        'مجدل شمس'       => ['Majdal Shams', "מג'דל שמס"],
        'ترشيحا'         => ['Tarshiha', 'תרשיחא'],
        'جسر الزرقاء'    => ['Jisr az-Zarqa', "ג'סר א-זרקא"],
        'الفريديس'       => ['Fureidis', 'פוריידיס'],
        'عرابة البطوف'   => ['Arraba', 'עראבה'],

        // ── ضواحي القدس ──
        'قلنديا'              => ['Qalandiya', 'קלנדיה'],
        'الرام'               => ['Ar Ram', 'א-ראם'],
        'العيزرية'            => ['Al Eizariya', 'אל-עיזריה'],
        'أبو ديس'             => ['Abu Dis', 'אבו דיס'],
        'السواحرة الشرقية'    => ['As Sawahira ash Sharqiya', 'א-סוואחרה א-שרקייה'],
        'حزما'                => ['Hizma', 'חיזמה'],
        'عناتا'               => ['Anata', 'ענאתא'],
        'بدو'                 => ['Biddu', 'בידו'],
        'قطنة'                => ['Qatanna', 'קטנה'],
        'بيت سوريك'           => ['Beit Surik', 'בית סוריכ'],
        'بيت إكسا'            => ['Beit Iksa', 'בית איכסא'],
        'الجيب'               => ['Al Jib', "אל-ג'יב"],
        'بير نبالا'           => ['Bir Nabala', 'ביר נבאלא'],
        'الجديرة'             => ['Al Judeira', "אל-ג'דירה"],
        'بيت دقو'             => ['Beit Duqqu', 'בית דוקו'],
        'بيت عنان'            => ['Beit Anan', 'בית ענאן'],
        'القبيبة'             => ['Al Qubeiba', 'אל-קוביבה'],
        'رافات القدس'         => ['Rafat Jerusalem', 'רפאת'],
        'الزعيم'              => ['Az Zaayyem', 'א-זעיים'],
        'الخان الأحمر'        => ['Khan al-Ahmar', "ח'אן אל-אחמר"],
        'النبي صموئيل'        => ['An Nabi Samwil', 'נבי סמואל'],
    ];

    public function handle(): int
    {
        $force   = (bool) $this->option('force');
        $updated = 0;
        $skipped = 0;

        foreach (DeliveryZone::all() as $zone) {
            $t = self::NAMES[trim($zone->name_ar)] ?? null;

            if (! $t) {
                // Retired legacy rows land here; they're inactive so it's fine.
                $this->line('— بلا ترجمة: ' . $zone->name_ar . ($zone->is_active ? ' ⚠️ (نشطة)' : ' (معطّلة)'));
                $skipped++;
                continue;
            }

            [$en, $he] = $t;
            $dirty = false;

            if ($force || blank($zone->name_en)) {
                $zone->name_en = $en;
                $dirty = true;
            }
            if ($force || blank($zone->name_he)) {
                $zone->name_he = $he;
                $dirty = true;
            }

            if ($dirty) {
                $zone->save();
                $updated++;
            }
        }

        $this->newLine();
        $this->info("حُدّثت {$updated} منطقة، وتُخطّيت {$skipped}.");

        $missing = DeliveryZone::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('name_he')->orWhere('name_he', ''))
            ->pluck('name_ar');

        if ($missing->isNotEmpty()) {
            $this->warn('مناطق نشطة ما زالت بلا اسم عبري: ' . $missing->implode('، '));
        } else {
            $this->info('✓ كل المناطق النشطة مترجمة.');
        }

        return self::SUCCESS;
    }
}
