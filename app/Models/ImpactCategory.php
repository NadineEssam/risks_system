<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** فئة الأثر (L2) — مثال: خسائر مالية مباشرة (FIN) */
class ImpactCategory extends Model
{
    use HasArabicAudit;

    protected $table = 'impact_categories';
    public $timestamps = false;
    protected $fillable = ['category_code', 'category_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity'];

    protected function casts(): array
    {
        return ['creation_date' => 'datetime', 'update_date' => 'datetime', 'validity' => 'boolean'];
    }

    public function items() { return $this->hasMany(ImpactItem::class); }

    public function scopeActive($query) { return $query->where('validity', true); }
}