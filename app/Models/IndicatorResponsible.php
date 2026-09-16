<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * INDICATOR_RESPONSIBLES — مسئولو المؤشر.
 */
class IndicatorResponsible extends Model
{
    use HasArabicAudit;

    protected $table = 'indicator_responsibles';

    public $timestamps = false;

    protected $fillable = [
        'indicators_id', 'responsible_role_id', 'full_name', 'job_title', 'email',
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

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicators_id');
    }

    public function role()
    {
        return $this->belongsTo(ResponsibleRole::class, 'responsible_role_id');
    }
}
