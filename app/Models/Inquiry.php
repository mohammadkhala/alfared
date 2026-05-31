<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name', 'phone', 'subject', 'message',
        'status', 'admin_reply', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /* ── Scopes ── */
    public function scopeNew($q)     { return $q->where('status', 'new'); }
    public function scopeRead($q)    { return $q->where('status', 'read'); }
    public function scopeReplied($q) { return $q->where('status', 'replied'); }

    /* ── Helpers ── */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'new'     => 'جديد',
            'read'    => 'مقروء',
            'replied' => 'تم الرد',
            default   => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new'     => 'danger',
            'read'    => 'warning',
            'replied' => 'success',
            default   => 'gray',
        };
    }
}
