<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * POTENTIAL_RISK_REGISTER_SECTOR_DETAILS — تفاصيل قطاعات الخطر.
 */
class PotentialRiskRegisterSectorDetail extends Model
{
    protected $table = 'potential_risk_register_sector_details';

    public $timestamps = false;

    protected $fillable = [
        'potential_risk_register_id', 'sectors_sec_id', 'creation_date', 'created_by',
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

    public function potentialRiskRegister()
    {
        return $this->belongsTo(PotentialRiskRegister::class, 'potential_risk_register_id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sectors_sec_id', 'sec_id');
    }

    public function requiredActions()
    {
        return $this->hasMany(RequiredAction::class, 'potential_risk_register_sector_details_id');
    }
}
