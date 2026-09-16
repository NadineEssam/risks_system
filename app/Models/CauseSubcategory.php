<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * CAUSE_SUBCATEGORY — تصنيف الأسباب L3.
 */
class CauseSubcategory extends Model
{
    use HasArabicAudit;

    protected $table = 'cause_subcategories';

    public $timestamps = false;

    protected $fillable = [
        'cause_category_id', 'subcategory_code', 'subcategory_name',
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

    public function causeCategory()
    {
        return $this->belongsTo(CauseCategory::class);
    }

    public function causeDetails()
    {
        return $this->hasMany(CauseDetail::class);
    }

    public function scopeActive($query)
    {
        return $query->where('validity', true);
    }
}
