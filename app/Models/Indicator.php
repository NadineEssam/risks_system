<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * INDICATORS — المؤشرات (المرحلة الرابعة - KRI).
 */
class Indicator extends Model
{
    use HasArabicAudit;

    protected $table = 'indicators';

    public $timestamps = false;

    protected $fillable = [
        'potential_risk_register_id', 'indicator_nature_id', 'measurement_unit_id',
        'reporting_frequency_id', 'activity_unit_id', 'indicator_name', 'related_actions',
        'data_sources', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            // "يُحفظ المؤشر بالحالة (مفعل) تلقائياً"
            if (! isset($model->attributes['validity'])) {
                $model->validity = true;
            }
        });
    }

    public function potentialRiskRegister()
    {
        return $this->belongsTo(PotentialRiskRegister::class, 'potential_risk_register_id');
    }

    public function nature()
    {
        return $this->belongsTo(IndicatorNature::class, 'indicator_nature_id');
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit_id');
    }

    public function reportingFrequency()
    {
        return $this->belongsTo(ReportingFrequency::class, 'reporting_frequency_id');
    }

    public function activityUnit()
    {
        return $this->belongsTo(ActivityUnit::class, 'activity_unit_id');
    }

    public function thresholdDetails()
    {
        return $this->hasMany(IndicatorThresholdDetail::class, 'indicators_id');
    }

    public function responsibles()
    {
        return $this->hasMany(IndicatorResponsible::class, 'indicators_id');
    }

    public function followups()
    {
        return $this->hasMany(IndicatorFollowup::class, 'indicators_id');
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }

    /** المؤشرات المرتبطة بقطاع إداري معين (عبر سجل الخطر المحتمل المرتبط) */
    public function scopeForSector($query, int $sectorSecId)
    {
        return $query->whereHas('potentialRiskRegister.sectorDetails', function ($q) use ($sectorSecId) {
            $q->where('sectors_sec_id', $sectorSecId);
        });
    }
}
