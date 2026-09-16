<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * INDICATOR_FOLLOWUP — متابعة المؤشر (المرحلة الخامسة).
 */
class IndicatorFollowup extends Model
{
    use HasArabicAudit;

    protected $table = 'indicator_followups';

    public $timestamps = false;

    protected $fillable = [
        'indicators_id', 'threshold_level_id', 'measurement_date', 'actual_value',
        'change_reason', 'action_taken', 'notes',
        'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'measurement_date' => 'date',
            'actual_value' => 'decimal:4',
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicators_id');
    }

    public function thresholdLevel()
    {
        return $this->belongsTo(ThresholdLevel::class, 'threshold_level_id');
    }
}
