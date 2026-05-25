<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisit extends Model
{
    protected $fillable = [
        'visitor_id', 'user_id', 'ip', 'device_type', 'browser', 'os',
        'url', 'path', 'referer', 'country', 'language', 'user_agent', 'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public static array $deviceLabels = [
        'app'     => '📱 تطبيق الجوال',
        'mobile'  => '📲 موبايل (متصفح)',
        'tablet'  => '🖥️ تابلت',
        'desktop' => '💻 سطح المكتب',
        'bot'     => '🤖 روبوت',
        'other'   => '❓ غير معروف',
    ];

    public static array $deviceColors = [
        'app'     => '#10B981',
        'mobile'  => '#3B82F6',
        'tablet'  => '#F59E0B',
        'desktop' => '#8B5CF6',
        'bot'     => '#6B7280',
        'other'   => '#94A3B8',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
