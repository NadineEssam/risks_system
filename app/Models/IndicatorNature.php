<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/** INDICATOR_NATURE — طبيعة المؤشر (متزايد / متناقص). */
class IndicatorNature extends Model
{
    use HasArabicAudit;

    protected $table = 'indicator_natures';

    public $timestamps = false;

    protected $fillable = [
        'nature_name', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
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
