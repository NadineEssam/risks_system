<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * INCIDENT_SECTORS_RESPONSIBILITIES — قطاعات ومسئوليات الحدث.
 */
class IncidentSectorResponsibility extends Model
{
    protected $table = 'incident_sectors_responsibilities';

    public $timestamps = false;

    protected $fillable = [
        'incident_id', 'sectors_sec_id', 'creation_date', 'created_by',
    ];

    protected function casts(): array
    {
        return ['creation_date' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->created_by) && auth()->check()) {
                $model->created_by = auth()->user()->name;
            }
            if (empty($model->creation_date)) {
                $model->creation_date = now();
            }
        });
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sectors_sec_id', 'sec_id');
    }

    public function followups()
    {
        return $this->hasMany(IncidentFollowup::class, 'incident_sectors_responsibilities_id');
    }

    /** آخر إدخال متابعة لهذه المسئولية القطاعية */
    public function latestFollowup()
    {
        return $this->hasOne(IncidentFollowup::class, 'incident_sectors_responsibilities_id')
            ->latestOfMany('creation_date');
    }
}
