<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;

/**
 * البيانات المرجعية (Lookups) اللازمة لتشغيل مسارات العمل الخمسة،
 * مبنية على التصنيفات المذكورة صراحةً في risks_ERD.mmd ووثيقة المتطلبات.
 */
class LookupDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBaselClassification();

        foreach (['حل كلي', 'حل جزئي', 'غير مقبول', 'مقبول'] as $name) {
            ResolutionStatus::firstOrCreate(['status_name' => $name]);
        }

        foreach (['تم الحل', 'إغلاق', 'جارى المتابعة', 'قبول الخطر'] as $name) {
            FollowupStatus::firstOrCreate(['status_name' => $name]);
        }

        foreach (['توصية', 'رد', 'رأى'] as $name) {
            FollowupEntryType::firstOrCreate(['type_name' => $name]);
        }

        foreach (['متزايد', 'متناقص'] as $name) {
            IndicatorNature::firstOrCreate(['nature_name' => $name]);
        }

        foreach (['عدد', 'نسبة%', 'يوم', 'ساعة', 'دقيقة'] as $name) {
            MeasurementUnit::firstOrCreate(['unit_name' => $name]);
        }

        foreach (['شهري', 'ربع سنوي', 'نصف سنوي', 'سنوي'] as $name) {
            ReportingFrequency::firstOrCreate(['frequency_name' => $name]);
        }

        foreach ([
            'إدارة الأصول',
            'تمويل هيكلة الشراكات',
            'تمويل شراكات',
            'أعمال استثمارات',
        ] as $name) {
            ActivityUnit::firstOrCreate(['unit_name' => $name]);
        }

        $thresholds = [
            'مستوى المخاطر المقبولة' => 1,
            'مستوى متوسط' => 2,
            'مستوى مرتفع' => 3,
        ];
        foreach ($thresholds as $name => $order) {
            ThresholdLevel::firstOrCreate(['level_name' => $name], ['sort_order' => $order]);
        }

        foreach (['مسئول متابعة وإبلاغ', 'مسئول اعتماد المؤشر'] as $name) {
            ResponsibleRole::firstOrCreate(['role_name' => $name]);
        }
    }

    /**
     * تصنيف بازل الأربعة مستويات: العام (EVENT_TYPE) → التفصيلي (EVENTS)
     * → الفرعي (EVENT_SUBCATEGORY) → الدقيق (EVENT_DETAIL). البيانات
     * الحقيقية مأخوذة من ملف "تصنيف بازل المخاطر موحد.xlsx" (شيت "تصنيف
     * احداث متقدم") الذي أرسلته المستخدمة بتاريخ 2026-09-09، ومحفوظة في
     * database/seeders/data/basel_events_classification.json (7 فئات عامة،
     * 20 تفصيلي، 57 فرعي، 114 دقيق). أكواد L1/L2 من الشيت لم تُحفَظ عمداً
     * (بطلب المستخدمة) لأن جدولي event_types/events لا يملكان عمود "كود"
     * أصلاً؛ أكواد L3/L4 محفوظة في subcategory_code/detail_code.
     */
    private function seedBaselClassification(): void
    {
        $path = database_path('seeders/data/basel_events_classification.json');

        if (! file_exists($path)) {
            return;
        }

        $types = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        foreach ($types as $typeData) {
            $type = EventType::firstOrCreate(['type_name' => $typeData['type_name']]);

            foreach ($typeData['events'] as $eventData) {
                $event = RiskEvent::firstOrCreate([
                    'event_type_id' => $type->id,
                    'event_name' => $eventData['event_name'],
                ]);

                foreach ($eventData['subcategories'] as $subcategoryData) {
                    $subcategory = EventSubcategory::firstOrCreate(
                        [
                            'events_id' => $event->id,
                            'subcategory_name' => $subcategoryData['subcategory_name'],
                        ],
                        ['subcategory_code' => $subcategoryData['subcategory_code']]
                    );

                    foreach ($subcategoryData['details'] as $detailData) {
                        EventDetail::firstOrCreate(
                            [
                                'event_subcategory_id' => $subcategory->id,
                                'detail_name' => $detailData['detail_name'],
                            ],
                            [
                                'detail_code' => $detailData['detail_code'],
                                'bank_example' => $detailData['bank_example'],
                            ]
                        );
                    }
                }
            }
        }
    }
}
