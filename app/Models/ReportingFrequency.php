<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** REPORTING_FREQUENCY — دورية الإبلاغ. */
class ReportingFrequency extends Model
{
    use HasArabicAudit;

    protected $table = 'reporting_frequencies';

    public $timestamps = false;

    protected $fillable = [
        'frequency_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
    ];

    protected function casts(): array
    {
        return [
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
