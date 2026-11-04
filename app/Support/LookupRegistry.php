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
use App\Models\CauseCategory;
use App\Models\CauseDetail;
use App\Models\CauseSubcategory;
use App\Models\ControlCategory;
use App\Models\ControlDomain;
use App\Models\ImpactCategory;
use App\Models\ImpactItem;

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
                    ['name' => 'type_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
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
                    ['name' => 'event_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
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

            // ===================== تصنيف الأسباب (3 مستويات) — اختيار واحد للخطر =====================
            'cause-categories' => [
                'model'    => CauseCategory::class,
                'title'    => 'الأسباب — المستوى الأول',
                'singular' => 'تصنيف سبب',
                'fields'   => [
                    ['name' => 'category_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                    ['name' => 'category_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                ],
                'usage' => [['cause_subcategories', 'cause_category_id', 'فئات الأسباب']],
            ],
            'cause-subcategories' => [
                'model'    => CauseSubcategory::class,
                'title'    => 'فئات الأسباب',
                'singular' => 'فئة سبب',
                'with'     => ['causeCategory'],
                'fields'   => [
                    ['name' => 'subcategory_name', 'label' => 'اسم الفئة', 'type' => 'text', 'required' => true],
                    ['name' => 'subcategory_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                    ['name' => 'cause_category_id', 'label' => 'المستوى الأول', 'type' => 'select', 'required' => true,
                        'options' => CauseCategory::class, 'display' => 'category_name', 'relation' => 'causeCategory'],
                ],
                'usage' => [['cause_details', 'cause_subcategory_id', 'الأسباب']],
            ],
            'cause-details' => [
                'model'    => CauseDetail::class,
                'title'    => 'الأسباب',
                'singular' => 'سبب',
                'with'     => ['causeSubcategory'],
                'fields'   => [
                    ['name' => 'detail_name', 'label' => 'السبب', 'type' => 'text', 'required' => true],
                    ['name' => 'detail_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                    ['name' => 'cause_subcategory_id', 'label' => 'فئة السبب', 'type' => 'select', 'required' => true,
                        'options' => CauseSubcategory::class, 'display' => 'subcategory_name', 'relation' => 'causeSubcategory'],
                ],
                'usage' => [['potential_risk_registers', 'cause_detail_id', 'المخاطر المحتملة']],
            ],

            // ===================== تصنيف الأثر — أكتر من اختيار للخطر =====================
            'impact-categories' => [
                'model'    => ImpactCategory::class,
                'title'    => 'فئات الأثر',
                'singular' => 'فئة أثر',
                'fields'   => [
                    ['name' => 'category_name', 'label' => 'اسم الفئة', 'type' => 'text', 'required' => true],
                    ['name' => 'category_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                ],
                'usage' => [['impact_items', 'impact_category_id', 'الآثار']],
            ],
            'impact-items' => [
                'model'    => ImpactItem::class,
                'title'    => 'الآثار',
                'singular' => 'أثر',
                'with'     => ['impactCategory'],
                'fields'   => [
                    ['name' => 'item_name', 'label' => 'الأثر', 'type' => 'text', 'required' => true],
                    ['name' => 'item_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                    ['name' => 'impact_category_id', 'label' => 'فئة الأثر', 'type' => 'select', 'required' => true,
                        'options' => ImpactCategory::class, 'display' => 'category_name', 'relation' => 'impactCategory'],
                    ['name' => 'nature_name', 'label' => 'طبيعة الأثر', 'type' => 'text', 'required' => false],
                    ['name' => 'nature_code', 'label' => 'كود الطبيعة', 'type' => 'text', 'required' => false],
                ],
                'usage' => [['prr_impact_items', 'impact_item_id', 'المخاطر المحتملة']],
            ],

            // ===================== تصنيف الضوابط — أكتر من اختيار للخطر =====================
            'control-domains' => [
                'model'    => ControlDomain::class,
                'title'    => 'مجالات الضوابط',
                'singular' => 'مجال ضابط',
                'fields'   => [
                    ['name' => 'domain_name', 'label' => 'المجال', 'type' => 'text', 'required' => true],
                    ['name' => 'domain_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                ],
                'usage' => [['control_categories', 'control_domain_id', 'فئات الضوابط']],
            ],
            'control-categories' => [
                'model'    => ControlCategory::class,
                'title'    => 'فئات الضوابط',
                'singular' => 'فئة ضابط',
                'with'     => ['controlDomain'],
                'fields'   => [
                    ['name' => 'category_name', 'label' => 'فئة الضابط', 'type' => 'text', 'required' => true],
                    ['name' => 'category_code', 'label' => 'الكود', 'type' => 'text', 'required' => false],
                    ['name' => 'control_domain_id', 'label' => 'المجال', 'type' => 'select', 'required' => true,
                        'options' => ControlDomain::class, 'display' => 'domain_name', 'relation' => 'controlDomain'],
                ],
                'usage' => [['prr_control_categories', 'control_category_id', 'المخاطر المحتملة']],
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