<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** RESPONSIBLE_ROLE — دور المسئول (متابعة وإبلاغ / اعتماد المؤشر). */
class ResponsibleRole extends Model
{
    use HasArabicAudit;

    protected $table = 'responsible_roles';

    public $timestamps = false;

    protected $fillable = [
        'role_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
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
