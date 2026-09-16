<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * EVENT_TYPE — تصنيف بازل العام.
 */
class EventType extends Model
{
    use HasArabicAudit;

    protected $table = 'event_types';

    public $timestamps = false;

    protected $fillable = [
        'type_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function events()
    {
        return $this->hasMany(RiskEvent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
