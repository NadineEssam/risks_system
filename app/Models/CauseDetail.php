<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * CAUSE_DETAIL — تصنيف الأسباب L4 (السبب الجذري)، وهو المستوى الذي يُربط
 * به سجل المخاطر المحتملة اختيارياً عبر potential_risk_registers.cause_detail_id.
 */
class CauseDetail extends Model
{
    use HasArabicAudit;

    protected $table = 'cause_details';

    public $timestamps = false;

    protected $fillable = [
        'cause_subcategory_id', 'detail_code', 'detail_name',
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

    public function causeSubcategory()
    {
        return $this->belongsTo(CauseSubcategory::class);
    }

    public function potentialRiskRegisters()
    {
        return $this->hasMany(PotentialRiskRegister::class, 'cause_detail_id');
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
