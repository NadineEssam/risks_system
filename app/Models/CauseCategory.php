<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * CAUSE_CATEGORY — تصنيف الأسباب L2 (المستوى الأعلى)، مستقل عن تصنيف بازل
 * للأحداث (EVENT_TYPE/EVENTS).
 */
class CauseCategory extends Model
{
    use HasArabicAudit;

    protected $table = 'cause_categories';

    public $timestamps = false;

    protected $fillable = [
        'category_code', 'category_name',
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

    public function subcategories()
    {
        return $this->hasMany(CauseSubcategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
