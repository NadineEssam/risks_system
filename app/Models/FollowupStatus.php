<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * FOLLOWUP_STATUS — حالة المتابعة (تم الحل / إغلاق / جارى المتابعة / قبول الخطر).
 */
class FollowupStatus extends Model
{
    use HasArabicAudit;

    protected $table = 'followup_statuses';

    public $timestamps = false;

    protected $fillable = [
        'status_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
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
        return $query->where('validity', true);
    }

    public const RESOLVED = 'تم الحل';

    public const CLOSED = 'إغلاق';

    public const IN_PROGRESS = 'جارى المتابعة';

    public const RISK_ACCEPTED = 'قبول الخطر';
}
