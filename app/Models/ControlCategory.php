<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** فئة الضابط (L3) — اللي بتتربط بالخطر المحتمل */
class ControlCategory extends Model
{
    use HasArabicAudit;

    protected $table = 'control_categories';
    public $timestamps = false;
    protected $fillable = ['control_domain_id', 'category_code', 'category_name',
        'creation_date', 'update_date', 'created_by', 'updated_by', 'validity'];

    protected function casts(): array
    {
        return ['creation_date' => 'datetime', 'update_date' => 'datetime', 'validity' => 'boolean'];
    }

    public function controlDomain() { return $this->belongsTo(ControlDomain::class); }

    public function scopeActive($query) { return $query->where('validity', true); }
}