<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** ACTIVITY_UNIT — وحدة النشاط. */
class ActivityUnit extends Model
{
    use HasArabicAudit;

    protected $table = 'activity_units';

    public $timestamps = false;

    protected $fillable = [
        'unit_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
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
