<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** مجال الضابط (L2) — مثال: الصلاحيات والفصل بين المهام (CTRL-ACC) */
class ControlDomain extends Model
{
    use HasArabicAudit;

    protected $table = 'control_domains';
    public $timestamps = false;
    protected $fillable = ['domain_code', 'domain_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity'];

    protected function casts(): array
    {
        return ['creation_date' => 'datetime', 'update_date' => 'datetime', 'validity' => 'boolean'];
    }

    public function categories() { return $this->hasMany(ControlCategory::class); }

    public function scopeActive($query) { return $query->where('validity', true); }
}