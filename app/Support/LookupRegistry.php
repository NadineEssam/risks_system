<?php

namespace App\Support;

use App\Models\ActivityUnit;
use App\Models\FollowupEntryType;
use App\Models\FollowupStatus;
use App\Models\IndicatorNature;
use App\Models\MeasurementUnit;
use App\Models\ReportingFrequency;
use App\Models\ResolutionStatus;
use App\Models\ResponsibleRole;
use App\Models\ThresholdLevel;

/**
 * سجل البيانات المرجعية (Lookups) ذات العمود الواحد، لإدارتها جميعاً
 * من خلال متحكم عام واحد (Admin\LookupController) بدلاً من تكرار نفس
 * الكود الأساسي لعشرة جداول متشابهة.
 */
class LookupRegistry
{
    public static function definitions(): array
    {
        return [
            'event-types' => [
                'model' => \App\Models\EventType::class,
                'field' => 'type_name',
                'title' => 'تصنيف بازل العام',
                'label' => 'اسم التصنيف',
            ],
            'resolution-statuses' => [
                'model' => ResolutionStatus::class,
                'field' => 'status_name',
                'title' => 'حالات حل الخطر',
                'label' => 'اسم الحالة',
            ],
            'followup-statuses' => [
                'model' => FollowupStatus::class,
                'field' => 'status_name',
                'title' => 'حالات المتابعة',
                'label' => 'اسم الحالة',
            ],
            'followup-entry-types' => [
                'model' => FollowupEntryType::class,
                'field' => 'type_name',
                'title' => 'أنواع إدخال المتابعة',
                'label' => 'اسم النوع',
            ],
            'indicator-natures' => [
                'model' => IndicatorNature::class,
                'field' => 'nature_name',
                'title' => 'طبيعة المؤشر',
                'label' => 'الاسم',
            ],
            'measurement-units' => [
                'model' => MeasurementUnit::class,
                'field' => 'unit_name',
                'title' => 'وحدات القياس',
                'label' => 'اسم الوحدة',
            ],
            'reporting-frequencies' => [
                'model' => ReportingFrequency::class,
                'field' => 'frequency_name',
                'title' => 'دورية الإبلاغ',
                'label' => 'اسم الدورية',
            ],
            'activity-units' => [
                'model' => ActivityUnit::class,
                'field' => 'unit_name',
                'title' => 'وحدات النشاط',
                'label' => 'اسم الوحدة',
            ],
            'responsible-roles' => [
                'model' => ResponsibleRole::class,
                'field' => 'role_name',
                'title' => 'أدوار المسئولين',
                'label' => 'اسم الدور',
            ],
            'threshold-levels' => [
                'model' => ThresholdLevel::class,
                'field' => 'level_name',
                'title' => 'مستويات الحدود',
                'label' => 'اسم المستوى',
                'has_sort_order' => true,
            ],
        ];
    }

    public static function find(string $type): ?array
    {
        return self::definitions()[$type] ?? null;
    }
}
