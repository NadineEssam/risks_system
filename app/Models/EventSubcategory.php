<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * EVENT_SUBCATEGORY — تصنيف بازل الفرعي (L3)، يقع بين "تصنيف بازل التفصيلي"
 * (events / L2) و"تصنيف بازل الدقيق" (event_details / L4).
 */
class EventSubcategory extends Model
{
    use HasArabicAudit;

    protected $table = 'event_subcategories';

    public $timestamps = false;

    protected $fillable = [
        'events_id', 'subcategory_code', 'subcategory_name',
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

    public function event()
    {
        return $this->belongsTo(RiskEvent::class, 'events_id');
    }

    public function eventDetails()
    {
        return $this->hasMany(EventDetail::class);
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
