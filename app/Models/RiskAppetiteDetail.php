<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** شهية المخاطر: حد مستوى واحد (مقبول/متوسط/مرتفع) لخطر محتمل */
class RiskAppetiteDetail extends Model
{
    use HasArabicAudit;

    protected $table = 'risk_appetite_details';
    public $timestamps = false;
    protected $fillable = ['potential_risk_register_id', 'threshold_level_id', 'threshold_value', 'required_action',
        'creation_date', 'update_date', 'created_by', 'updated_by'];

    protected function casts(): array
    {
        return ['creation_date' => 'datetime', 'update_date' => 'datetime', 'threshold_value' => 'integer'];
    }

    public function thresholdLevel() { return $this->belongsTo(ThresholdLevel::class); }

    public function potentialRiskRegister() { return $this->belongsTo(PotentialRiskRegister::class); }
}