<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * INCIDENT_FOLLOWUP — متابعة الحدث (المرحلة الثالثة).
 */
class IncidentFollowup extends Model
{
    use HasArabicAudit;

    protected $table = 'incident_followups';

    public $timestamps = false;

    protected $fillable = [
        'incident_sectors_responsibilities_id', 'followup_status_id', 'followup_entry_type_id',
        'followup_date', 'entry_text', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'followup_date' => 'date',
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function incidentSectorResponsibility()
    {
        return $this->belongsTo(IncidentSectorResponsibility::class, 'incident_sectors_responsibilities_id');
    }

    public function followupStatus()
    {
        return $this->belongsTo(FollowupStatus::class, 'followup_status_id');
    }

    public function followupEntryType()
    {
        return $this->belongsTo(FollowupEntryType::class, 'followup_entry_type_id');
    }
}
