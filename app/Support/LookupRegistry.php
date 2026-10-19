<?php

namespace App\Support;

use App\Models\ActivityUnit;
use App\Models\EventDetail;
use App\Models\EventSubcategory;
use App\Models\EventType;
use App\Models\FollowupEntryType;
use App\Models\FollowupStatus;
use App\Models\IndicatorNature;
use App\Models\MeasurementUnit;
use App\Models\ReportingFrequency;
use App\Models\ResolutionStatus;
use App\Models\ResponsibleRole;
use App\Models\RiskEvent;
use App\Models\ThresholdLevel;

/**
 * تعريف كل البيانات المرجعية (Lookups) في مكان واحد — نظام CRUD عام واحد
 * (LookupController + LookupDataTable) بيشتغل على الكل.
 *
 * fields: [name, label, type (text|textarea|number|select), required, list?,
 *          options/display/relation (للـ select)]
 * usage:  [جدول, عمود, وصف عربي] — لو في أي سجل بيستخدم القيمة، الحذف ممنوع
 */
class LookupRegistry
{
    public static function definitions(): array
    {
        return [
            // ===================== تصنيف بازل (4 مستويات) =====================
            'event-types' => [
                'model'    => EventType::class,
                'title'    => 'تصنيف بازل العام',
                'singular' => 'تصنيف عام',
                'fields'   => [
                    ['name' => 'type_name', 'label' => 'اسم التصنيف', 'type' => 'text', 'required' => true],
                ],
                'usage' => [['events', 'event_type_id', 'تصنيفات بازل التفصيلية']],
            ],
            'events' => [
                'model'    => RiskEvent::class,
                'title'    => 'تصنيف بازل التفصيلي',
                'singular' => 'تصنيف تفصيلي',
                'with'     => ['eventType'],
                'fields'   => [
                    ['name' => 'event_type_id', 'label' => 'تصنيف بازل العام', 'type' => 'select', 'required' => true,
                        'options' => EventType::class, 'display' => 'type_name', 'relation' => 'eventType'],
                    ['name' => 'event_name', 'label' => 'اسم التصنيف التفصيلي', 'type' => 'text', 'required' => true],
                ],
                'usage' => [['event_subcategories', 'events_id', 'تصنيفات بازل الفرعية']],
            ],
            'event-subcategories' => [
                'model'    => EventSubcategory::class,
                'title'    => 'تصنيف بازل الفرعي',
                'singular' => 'تصنيف فرعي',
                'with'     => ['event'],
                'fields'   => [
                    ['name' => 'events_id', 'label' => 'تصنيف بازل التفصيلي', 'type' => 'select', 'required' => true,
                        'options' => RiskEvent::class, 'display' => 'event_name', 'relation' => 'event'],
                    ['name' => 'subcategory_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                    ['name' => 'subcategory_name', 'label' => 'اسم التصنيف الفرعي', 'type' => 'text', 'required' => true],
                ],
                'usage' => [['event_details', 'event_subcategory_id', 'تصنيفات بازل الدقيقة']],
            ],
            'event-details' => [
                'model'    => EventDetail::class,
                'title'    => 'تصنيف بازل الدقيق',
                'singular' => 'تصنيف دقيق',
                'with'     => ['eventSubcategory'],
                'fields'   => [
                    ['name' => 'event_subcategory_id', 'label' => 'تصنيف بازل الفرعي', 'type' => 'select', 'required' => true,
                        'options' => EventSubcategory::class, 'display' => 'subcategory_name', 'relation' => 'eventSubcategory'],
                    ['name' => 'detail_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                    ['name' => 'detail_name', 'label' => 'اسم التصنيف الدقيق', 'type' => 'text', 'required' => true],
                    ['name' => 'bank_example', 'label' => 'مثال توضيحي', 'type' => 'textarea', 'required' => false, 'list' => false],
                ],
                'usage' => [['potential_risk_registers', 'event_detail_id', 'المخاطر المحتملة']],
            ],

            // ===================== بيانات مرجعية بسيطة =====================
            'resolution-statuses' => [
                'model'    => ResolutionStatus::class,
                'title'    => 'حالات حل الخطر',
                'singular' => 'حالة',
                'fields'   => [['name' => 'status_name', 'label' => 'اسم الحالة', 'type' => 'text', 'required' => true]],
                'usage'    => [
                    ['incidents', 'resolution_status_id', 'الأحداث'],
                    ['risk_status_details', 'resolution_status_id', 'سجل حالات المخاطر'],
                ],
            ],
            'followup-statuses' => [
                'model'    => FollowupStatus::class,
                'title'    => 'حالات المتابعة',
                'singular' => 'حالة متابعة',
                'note'     => 'أسماء الحالات "جارى المتابعة" و"إغلاق" و"قبول الخطر" مستخدمة في منطق متابعة الأحداث — تغيير الاسم يغيّر سلوك النظام.',
                'fields'   => [['name' => 'status_name', 'label' => 'اسم الحالة', 'type' => 'text', 'required' => true]],
                'usage'    => [['incident_followups', 'followup_status_id', 'متابعات الأحداث']],
            ],
            'followup-entry-types' => [
                'model'    => FollowupEntryType::class,
                'title'    => 'أنواع إدخال المتابعة',
                'singular' => 'نوع إدخال',
                'fields'   => [['name' => 'type_name', 'label' => 'اسم النوع', 'type' => 'text', 'required' => true]],
                'usage'    => [['incident_followups', 'followup_entry_type_id', 'متابعات الأحداث']],
            ],
            'indicator-natures' => [
                'model'    => IndicatorNature::class,
                'title'    => 'طبيعة المؤشر',
                'singular' => 'طبيعة',
                'note'     => 'الاسمان "متزايد" و"متناقص" مستخدمان في حساب مستوى حد الخطر — تغيير الاسم يغيّر الحساب.',
                'fields'   => [['name' => 'nature_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true]],
                'usage'    => [['indicators', 'indicator_nature_id', 'المؤشرات']],
            ],
            'measurement-units' => [
                'model'    => MeasurementUnit::class,
                'title'    => 'وحدات القياس',
                'singular' => 'وحدة قياس',
                'fields'   => [['name' => 'unit_name', 'label' => 'اسم الوحدة', 'type' => 'text', 'required' => true]],
                'usage'    => [['indicators', 'measurement_unit_id', 'المؤشرات']],
            ],
            'reporting-frequencies' => [
                'model'    => ReportingFrequency::class,
                'title'    => 'دورية الإبلاغ',
                'singular' => 'دورية',
                'fields'   => [['name' => 'frequency_name', 'label' => 'اسم الدورية', 'type' => 'text', 'required' => true]],
                'usage'    => [['indicators', 'reporting_frequency_id', 'المؤشرات']],
            ],
            'activity-units' => [
                'model'    => ActivityUnit::class,
                'title'    => 'وحدات النشاط',
                'singular' => 'وحدة نشاط',
                'fields'   => [['name' => 'unit_name', 'label' => 'اسم الوحدة', 'type' => 'text', 'required' => true]],
                'usage'    => [['indicators', 'activity_unit_id', 'المؤشرات']],
            ],
            'responsible-roles' => [
                'model'    => ResponsibleRole::class,
                'title'    => 'أدوار المسئولين',
                'singular' => 'دور',
                'fields'   => [['name' => 'role_name', 'label' => 'اسم الدور', 'type' => 'text', 'required' => true]],
                'usage'    => [['indicator_responsibles', 'responsible_role_id', 'مسئولي المؤشرات']],
            ],
            'threshold-levels' => [
                'model'    => ThresholdLevel::class,
                'title'    => 'مستويات الحدود',
                'singular' => 'مستوى',
                'order'    => 'sort_order',
                'note'     => 'الترتيب مستخدم في حساب مستوى حد الخطر: 1 = مقبول، 2 = متوسط، 3 = مرتفع.',
                'fields'   => [
                    ['name' => 'sort_order', 'label' => 'الترتيب', 'type' => 'number', 'required' => true],
                    ['name' => 'level_name', 'label' => 'اسم المستوى', 'type' => 'text', 'required' => true],
                ],
                'usage' => [
                    ['indicator_thresholds_details', 'threshold_level_id', 'حدود المؤشرات'],
                    ['indicator_followups', 'threshold_level_id', 'قياسات المؤشرات'],
                ],
            ],
        ];
    }

    public static function find(string $type): ?array
    {
        return self::definitions()[$type] ?? null;
    }

    /** الحقل الأساسي (أول حقل نصي) — للعناوين ورسائل الحذف */
    public static function mainField(array $definition): string
    {
        foreach ($definition['fields'] as $field) {
            if ($field['type'] === 'text') {
                return $field['name'];
            }
        }

        return $definition['fields'][0]['name'];
    }
}