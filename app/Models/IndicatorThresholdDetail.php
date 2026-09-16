<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * INDICATOR_THRESHOLDS_DETAILS — تفاصيل حدود المؤشر (3 مستويات لكل مؤشر).
 */
class IndicatorThresholdDetail extends Model
{
    use HasArabicAudit;

    protected $table = 'indicator_thresholds_details';

    public $timestamps = false;

    protected $fillable = [
        'indicators_id', 'threshold_level_id', 'threshold_value', 'required_action',
        'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'threshold_value' => 'decimal:4',
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
