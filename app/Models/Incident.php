<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;
use App\Support\IncidentAccess;
/**
 * INCIDENT — الحدث (المرحلة الثانية).
 */
class Incident extends Model
{
    use HasArabicAudit;

    protected $table = 'incidents';

    public $timestamps = false;

    protected $fillable = [
        'potential_risk_register_id', 'resolution_status_id', 'departments_dep_id',
        'start_date', 'discovery_date', 'impact_score', 'frequency_score', 'risk_degree',
        'description', 'current_procedure', 'proposed_procedure', 'actual_impact_problem',
        'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'discovery_date' => 'date',
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function potentialRiskRegister()
    {
        return $this->belongsTo(PotentialRiskRegister::class, 'potential_risk_register_id');
    }

    public function resolutionStatus()
    {
        return $this->belongsTo(ResolutionStatus::class, 'resolution_status_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'departments_dep_id', 'dep_id');
    }

    public function sectorResponsibilities()
    {
        return $this->hasMany(IncidentSectorResponsibility::class, 'incident_id');
    }

    /** كل متابعات الحدث من كل القطاعات */
    public function followups()
    {
        return $this->hasManyThrough(
            IncidentFollowup::class,
            IncidentSectorResponsibility::class,
            'incident_id',                          // FK على incident_sectors_responsibilities
            'incident_sectors_responsibilities_id', // FK على incident_followups
            'id',
            'id'
        );
    }

    /** آخر متابعة على الحدث (زي آخر رد على البيان في الشكاوى) */
    public function lastFollowup(): ?IncidentFollowup
    {
        return $this->followups()->with('followupStatus')->reorder()->orderByDesc('incident_followups.id')->first();
    }

    /** الحدث مقفول لو آخر متابعة حالتها إغلاق أو قبول الخطر */
    public function isFollowupClosed(): bool
    {
        return in_array($this->lastFollowup()?->followupStatus?->status_name, IncidentAccess::CLOSING_STATUSES, true);
    }

    // public function responsibleSectors()
    // {
    //     return $this->belongsToMany(
    //         Sector::class,
    //         'incident_sectors_responsibilities',
    //         'incident_id',
    //         'sectors_sec_id',
    //         'id',
    //         'sec_id'
    //     );
    // }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }

    /**
     * عدد مرات التكرار الفعلي لخطر محتمل معيّن على مستوى قطاع إداري محدد
     * (يُستخدم لاحتساب frequency_score تلقائياً، بحد أقصى 5).
     */
    public static function actualRepetitionCount(string $potentialRiskRegisterId, int $sectorSecId): int
    {
        $sectorCode = Sector::where('sec_id', $sectorSecId)->value('sector_code');

        if (! $sectorCode) {
            return 0;
        }

        // الإدارات في new_po (MySQL) والأحداث في Oracle — لا يمكن whereHas بين قاعدتين
        $depIds = Department::where('sector_code', $sectorCode)->pluck('dep_id');

        return static::query()
            ->where('potential_risk_register_id', $potentialRiskRegisterId)
            ->whereIn('departments_dep_id', $depIds)
            ->count();
    }

    public static function cappedFrequencyScore(int $actualCount): int
    {
        return min($actualCount, 5);
    }
}
