<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** الأثر (L3) — وطبيعة الأثر (مالي/غير مالي) خاصية عليه مش مستوى أعلى */
class ImpactItem extends Model
{
    use HasArabicAudit;

    protected $table = 'impact_items';
    public $timestamps = false;
    protected $fillable = ['impact_category_id', 'item_code', 'item_name', 'nature_code', 'nature_name',
        'creation_date', 'update_date', 'created_by', 'updated_by', 'validity'];

    protected function casts(): array
    {
        return ['creation_date' => 'datetime', 'update_date' => 'datetime', 'validity' => 'boolean'];
    }

    public function impactCategory() { return $this->belongsTo(ImpactCategory::class); }

    public function scopeActive($query) { return $query->where('validity', true); }
}