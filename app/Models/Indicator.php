<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;
use App\Models\ThresholdLevel;
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

    /** آخر قياس (بالتاريخ ثم الأحدث تسجيلاً) */
    public function lastFollowup(): ?IndicatorFollowup
    {
        return $this->followups()->with('thresholdLevel')
            ->orderByDesc('measurement_date')->orderByDesc('id')->first();
    }

    public function isDecreasing(): bool
    {
        return trim((string) $this->nature?->nature_name) === 'متناقص';
    }

    /**
     * حدود المتوسط والمرتفع (sort_order: 1 مقبول، 2 متوسط، 3 مرتفع)
     * @return array{medium: ?float, high: ?float}
     */
    public function thresholdLimits(): array
    {
        $details = $this->thresholdDetails()->with('thresholdLevel')->get()
            ->keyBy(fn ($d) => (int) $d->thresholdLevel?->sort_order);

        return [
            'medium' => $details->get(2)?->threshold_value !== null ? (float) $details->get(2)->threshold_value : null,
            'high'   => $details->get(3)?->threshold_value !== null ? (float) $details->get(3)->threshold_value : null,
        ];
    }

    /**
     * مستوى حد الخطر تلقائياً من القيمة الفعلية + طبيعة المؤشر:
     * متزايد: < متوسط = مقبول | ≥ متوسط و < مرتفع = متوسط | ≥ مرتفع = مرتفع
     * متناقص: > متوسط = مقبول | ≤ متوسط و > مرتفع = متوسط | ≤ مرتفع = مرتفع
     */
    public function calculateThresholdLevel($value): ?ThresholdLevel
    {
        ['medium' => $medium, 'high' => $high] = $this->thresholdLimits();

        if ($value === null || $value === '' || ! is_numeric($value) || $medium === null || $high === null) {
            return null;
        }

        $v = (float) $value;

        $order = $this->isDecreasing()
            ? ($v <= $high ? 3 : ($v <= $medium ? 2 : 1))
            : ($v >= $high ? 3 : ($v >= $medium ? 2 : 1));

        return ThresholdLevel::where('sort_order', $order)->first();
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
