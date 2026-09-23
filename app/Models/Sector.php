<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * SECTORS — القطاعات الادارية. بيانات مرجعية تُدار محلياً من شاشة "القطاعات"
 * تحت "البيانات المرجعية" (إضافة + تفعيل/إلغاء تفعيل).
 */
class Sector extends Model
{
    protected $connection = 'new_po';

    protected $table = 'sectors';

    protected $primaryKey = 'sec_id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'sector_code',
        'sector_ar',
        'sector_en',
        'fk_govt_code',
        'fk_off_code',
        'create_user_id',
        'creation_date',
        'update_user_id',
        'update_date',
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

    public function departments()
    {
        return $this->hasMany(Department::class, 'sector_code', 'sector_code');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'sector_id', 'sec_id');
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
