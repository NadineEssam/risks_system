<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * EVENTS — تصنيف بازل التفصيلى (يُسمى RiskEvent في الكود لتجنب التعارض
 * مع Illuminate\Events\Event، والجدول الفعلي في قاعدة البيانات هو "events").
 */
class RiskEvent extends Model
{
    use HasArabicAudit;

    protected $table = 'events';

    public $timestamps = false;

    protected $fillable = [
        'event_type_id', 'event_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function subcategories()
    {
        return $this->hasMany(EventSubcategory::class, 'events_id');
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
