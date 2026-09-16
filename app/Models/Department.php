<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DEPARTMENTS — الإدارات. بيانات مرجعية تُدار محلياً من شاشة "الإدارات"
 * تحت "البيانات المرجعية" (إضافة + تفعيل/إلغاء تفعيل).
 */
class Department extends Model
{
    protected $table = 'departments';

    protected $primaryKey = 'dep_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'sector_code',
        'dep_code',
        'depname_en',
        'depname_ar',
        'create_user_id',
        'creation_date',
        'update_user_id',
        'update_date',
        'fk_govt_code',
        'fk_off_code',
        'validity',
    ];

    protected function casts(): array
    {
        return [
            'creation_date' => 'datetime',
            'update_date' => 'datetime',
            'validity' => 'boolean',
        ];
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_code', 'sector_code');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'dep_id');
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
