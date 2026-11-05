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

    /**
     * تصنيف درجة الخطر حسب شهية الخطر المحتمل (لو متحددة)، وإلا التقسيم الافتراضي (8 / 15).
     * @return array{label: string, class: string, order: int, action: ?string}|null
     */
    public static function classifyFor(?\App\Models\PotentialRiskRegister $risk, ?int $degree): ?array
    {
        if ($degree === null) {
            return null;
        }

        $limits = $risk ? $risk->appetiteLimits() : [];

        $order = isset($limits[2], $limits[3])
            ? ($degree >= $limits[3] ? 3 : ($degree >= $limits[2] ? 2 : 1))
            : ($degree >= 15 ? 3 : ($degree >= 8 ? 2 : 1));

        [$label, $class] = [
            1 => ['مقبول', 'bg-success'],
            2 => ['متوسط', 'bg-warning text-dark'],
            3 => ['مرتفع', 'bg-danger'],
        ][$order];

        $action = $risk?->appetiteDetails
            ->first(fn ($d) => (int) $d->thresholdLevel?->sort_order === $order)
            ?->required_action;

        return ['label' => $label, 'class' => $class, 'order' => $order, 'action' => $action];
    }

    /** شارة درجة خطر الحدث حسب شهية الخطر المرتبط */
    public static function badgeForIncident($incident): string
    {
        if (! $incident || $incident->risk_degree === null) {
            return '<span class="badge bg-secondary">غير محدد</span>';
        }

        $incident->loadMissing('potentialRiskRegister.appetiteDetails.thresholdLevel');
        $level = self::classifyFor($incident->potentialRiskRegister, (int) $incident->risk_degree);

        return sprintf('<span class="badge %s">%d - %s</span>', $level['class'], $incident->risk_degree, $level['label']);
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
