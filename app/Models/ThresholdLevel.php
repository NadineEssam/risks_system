<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** THRESHOLD_LEVEL — مستوى الحد (مقبول / متوسط / مرتفع). */
class ThresholdLevel extends Model
{
    use HasArabicAudit;

    protected $table = 'threshold_levels';

    public $timestamps = false;

    protected $fillable = [
        'level_name', 'sort_order', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true)->orderBy('sort_order');
    }

    public const ACCEPTABLE = 'مستوى المخاطر المقبولة';

    public const MEDIUM = 'مستوى متوسط';

    public const HIGH = 'مستوى مرتفع';

    public function isAcceptable(): bool
    {
        return trim($this->level_name) === self::ACCEPTABLE;
    }

        /** بادج ملوّن للمستوى: 🟢 مقبول — 🟠 متوسط — 🔴 مرتفع */
    public function badge(): string
    {
        $style = match ((int) $this->sort_order) {
            1       => 'background:#28a745;color:#fff;',
            2       => 'background:#f0ad4e;color:#fff;',
            3       => 'background:#dc3545;color:#fff;',
            default => 'background:#6c757d;color:#fff;',
        };

        return '<span class="badge" style="'.$style.'font-size:12px;padding:6px 10px;">'.e($this->level_name).'</span>';
    }

    public static function emptyBadge(string $text = 'لا يوجد قياس'): string
    {
        return '<span class="badge" style="background:#6c757d;color:#fff;font-size:12px;padding:6px 10px;">'.e($text).'</span>';
    }
}
