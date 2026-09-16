<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * REQUIRED_ACTIONS — الإجراءات المطلوبة من كل قطاع مسؤول عن الخطر المحتمل.
 */
class RequiredAction extends Model
{
    use HasArabicAudit;

    protected $table = 'required_actions';

    public $timestamps = false;

    protected $fillable = [
        'potential_risk_register_sector_details_id', 'required_action', 'expiration_date',
        'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function sectorDetail()
    {
        return $this->belongsTo(PotentialRiskRegisterSectorDetail::class, 'potential_risk_register_sector_details_id');
    }
}
