<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * RISK_STATUS_DETAILS — تفاصيل حالة حل الخطر (تاريخ الحالات المعتمدة).
 *
 * اسم الجدول تم تقصيره من "risk_resolution_status_details" (30 حرفًا) إلى
 * "risk_status_details" لتفادي تضارب اسم الـ sequence مع اسم الجدول في
 * أوراكل (ORA-00955) الناتج عن قصّ yajra/laravel-oci8 لأسماء الـ sequence
 * الطويلة إلى 30 حرفًا. اسم الكلاس نفسه لم يتغيّر حتى لا نُضطر لتعديل كل
 * الأماكن التي تستخدمه.
 */
class RiskResolutionStatusDetail extends Model
{
    protected $table = 'risk_status_details';

    public $timestamps = false;

    protected $fillable = [
        'potential_risk_register_id', 'resolution_status_id', 'creation_date', 'created_by',
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

    public function resolutionStatus()
    {
        return $this->belongsTo(ResolutionStatus::class, 'resolution_status_id');
    }
}
