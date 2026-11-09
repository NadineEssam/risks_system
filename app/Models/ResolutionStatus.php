<?php

namespace App\Models;

use App\Concerns\HasArabicAudit;
use Illuminate\Database\Eloquent\Model;

/**
 * RESOLUTION_STATUS — حالة حل الحدث (حل كلي / حل جزئي / غير مقبول / مقبول).
 */
class ResolutionStatus extends Model
{
    use HasArabicAudit;

    protected $table = 'resolution_statuses';

    public $timestamps = false;

    protected $fillable = [
        'status_name', 'status_group', 'creation_date', 'update_date', 'created_by', 'updated_by', 'validity',
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

    public const GROUP_ACCEPTANCE = 'acceptance'; // قبول الخطر: مقبول / غير مقبول
    public const GROUP_TREATMENT  = 'treatment';  // معالجة الخطر: حل كلي / حل جزئي

    public function scopeAcceptance($query) { return $query->where('status_group', self::GROUP_ACCEPTANCE); }

    public function scopeTreatment($query) { return $query->where('status_group', self::GROUP_TREATMENT); }

    // أسماء الحالات القياسية المستخدمة في منطق العمل
    public const FULLY_RESOLVED = 'حل كلي';

    public const PARTIALLY_RESOLVED = 'حل جزئي';

    public const UNACCEPTABLE = 'غير مقبول';

    public const ACCEPTABLE = 'مقبول';
}
