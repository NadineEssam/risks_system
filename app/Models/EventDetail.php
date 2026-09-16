<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * EVENT_DETAIL — تصنيف بازل الدقيق (L4)، أدق مستوى تصنيف، وهو المستوى
 * الذي يُربط به سجل المخاطر المحتملة فعلياً (potential_risk_registers).
 */
class EventDetail extends Model
{
    use HasArabicAudit;

    protected $table = 'event_details';

    public $timestamps = false;

    protected $fillable = [
        'event_subcategory_id', 'detail_code', 'detail_name', 'bank_example',
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

    public function eventSubcategory()
    {
        return $this->belongsTo(EventSubcategory::class);
    }

    public function potentialRiskRegisters()
    {
        return $this->hasMany(PotentialRiskRegister::class, 'event_detail_id');
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
