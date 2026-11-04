<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * POTENTIAL_RISK_REGISTER — سجل المخاطر المحتملة (المرحلة الأولى).
 */
class PotentialRiskRegister extends Model
{
    use HasArabicAudit;

    protected $table = 'potential_risk_registers';

    public $timestamps = false;

    protected $fillable = [
        'event_detail_id', 'risk_description', 'proposed_control',
        'cause_detail_id',
        'created_sector_id', 'created_department_id',
        'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function eventDetail()
    {
        return $this->belongsTo(EventDetail::class);
    }

    /**
     * آخر حالة للخطر في مجموعة معيّنة (acceptance = قبول الخطر / treatment = معالجة الخطر)
     * — من العلاقة المحمّلة resolutionStatusDetails.resolutionStatus
     */
    public function currentStatus(string $group): ?ResolutionStatus
    {
        return $this->resolutionStatusDetails
            ->filter(fn ($d) => $d->resolutionStatus?->status_group === $group)
            ->sortByDesc('id')
            ->first()
            ?->resolutionStatus;
    }

    /** تصنيفات ERM: السبب (L3) — الأثر (L3) — فئة الضابط (L3) */
    public function causeDetail() { return $this->belongsTo(CauseDetail::class); }

    /** الأثر: أكتر من اختيار */
    public function impactItems()
    {
        return $this->belongsToMany(ImpactItem::class, 'prr_impact_items', 'potential_risk_register_id', 'impact_item_id');
    }

    /** فئات الضوابط: أكتر من اختيار */
    public function controlCategories()
    {
        return $this->belongsToMany(ControlCategory::class, 'prr_control_categories', 'potential_risk_register_id', 'control_category_id');
    }

    /**
     * نص وصفي كامل لتصنيف بازل الأربعة مستويات (العام → التفصيلي → الفرعي
     * → الدقيق)، لاستخدامه في شاشات العرض والقوائم المنسدلة بدل الاعتماد
     * في كل شاشة على سلسلة علاقات طويلة. يتطلب eager-loading السلسلة
     * ('eventDetail.eventSubcategory.event.eventType') لتفادي N+1.
     */
    public function getClassificationLabelAttribute(): ?string
    {
        $detail = $this->eventDetail;

        if (! $detail) {
            return null;
        }

        $subcategory = $detail->eventSubcategory;
        $event = $subcategory?->event;
        $type = $event?->eventType;

        return collect([
            $type?->type_name,
            $event?->event_name,
            $subcategory?->subcategory_name,
            $detail->detail_name,
        ])->filter()->implode(' — ');
    }

    public function sectorDetails()
    {
        return $this->hasMany(PotentialRiskRegisterSectorDetail::class, 'potential_risk_register_id');
    }

    // public function sectors()
    // {
    //     return $this->belongsToMany(
    //         Sector::class,
    //         'potential_risk_register_sector_details',
    //         'potential_risk_register_id',
    //         'sectors_sec_id',
    //         'id',
    //         'sec_id'
    //     );
    // }

    public function resolutionStatusDetails()
    {
        return $this->hasMany(RiskResolutionStatusDetail::class, 'potential_risk_register_id');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class, 'potential_risk_register_id');
    }

    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'potential_risk_register_id');
    }

    /** آخر حالة حل مُعتمدة للخطر المحتمل */
    public function latestResolutionStatus()
    {
        return $this->hasOne(RiskResolutionStatusDetail::class, 'potential_risk_register_id')
            ->latestOfMany();
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }

    /** المخاطر المحتملة المرتبطة بقطاع إداري معين */
    public function scopeForSector($query, int $sectorSecId)
    {
        return $query->whereHas('sectorDetails', function ($q) use ($sectorSecId) {
            $q->where('sectors_sec_id', $sectorSecId);
        });
    }
}
