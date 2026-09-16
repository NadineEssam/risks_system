<?php

namespace App\Support;

/**
 * منطق تلوين درجة الخطر (Risk Degree = Impact × Frequency، الحد الأقصى 25)
 * لعرض شارة (badge) موحدة في كل الشاشات والتقارير.
 */
class RiskDegreeHelper
{
    public static function badge(?int $degree): string
    {
        if ($degree === null) {
            return '<span class="badge bg-secondary">غير محدد</span>';
        }

        [$label, $class] = self::classify($degree);

        return sprintf('<span class="badge %s">%d - %s</span>', $class, $degree, $label);
    }

    /** @return array{0: string, 1: string} */
    public static function classify(int $degree): array
    {
        return match (true) {
            $degree >= 15 => ['مرتفع', 'bg-danger'],
            $degree >= 8 => ['متوسط', 'bg-warning text-dark'],
            default => ['مقبول', 'bg-success'],
        };
    }
}
