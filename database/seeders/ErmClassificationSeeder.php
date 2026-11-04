<?php

namespace Database\Seeders;

use App\Models\CauseCategory;
use App\Models\CauseDetail;
use App\Models\CauseSubcategory;
use App\Models\ControlCategory;
use App\Models\ControlDomain;
use App\Models\EventDetail;
use App\Models\EventSubcategory;
use App\Models\EventType;
use App\Models\ImpactCategory;
use App\Models\ImpactItem;
use App\Models\RiskEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * تحميل تصنيف المخاطر النهائي (ERM) من erm_classification.json:
 * الأحداث (4 مستويات) — الأسباب (3) — الأثر (فئة + أثر) — الضوابط (مجال + فئة).
 * - آمن لإعادة التشغيل (بيدوّر بالكود).
 * - التصنيفات القديمة اللي مش في الملف بتتلغي تفعيلها (مش بتتحذف) عشان المخاطر القديمة.
 */
class ErmClassificationSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/erm_classification.json')), true);

        DB::transaction(function () use ($data) {
            $this->events($data['events']);
            $this->causes($data['causes']);
            $this->impacts($data['impacts']);
            $this->controls($data['controls']);
        });

        $this->command?->info('Events: '.EventDetail::active()->count()
            .' | Causes: '.CauseDetail::active()->count()
            .' | Impacts: '.ImpactItem::active()->count()
            .' | Controls: '.ControlCategory::active()->count());
    }

    private function events(array $rows): void
    {
        $keep = ['t' => [], 'e' => [], 's' => [], 'd' => []];

        foreach ($rows as $r) {
            $type = EventType::updateOrCreate(['type_code' => $r['l1_code']], ['type_name' => $r['l1'], 'validity' => true]);
            $event = RiskEvent::updateOrCreate(['event_code' => $r['l2_code']], ['event_type_id' => $type->id, 'event_name' => $r['l2'], 'validity' => true]);
            $sub = EventSubcategory::updateOrCreate(['subcategory_code' => $r['l3_code']], ['events_id' => $event->id, 'subcategory_name' => $r['l3'], 'validity' => true]);
            $det = EventDetail::updateOrCreate(['detail_code' => $r['l4_code']], ['event_subcategory_id' => $sub->id, 'detail_name' => $r['l4'], 'validity' => true]);

            $keep['t'][] = $type->id; $keep['e'][] = $event->id; $keep['s'][] = $sub->id; $keep['d'][] = $det->id;
        }

        $this->deactivateOthers(EventType::class, $keep['t']);
        $this->deactivateOthers(RiskEvent::class, $keep['e']);
        $this->deactivateOthers(EventSubcategory::class, $keep['s']);
        $this->deactivateOthers(EventDetail::class, $keep['d']);
    }

    private function causes(array $rows): void
    {
        $keep = ['c' => [], 's' => [], 'd' => []];

        foreach ($rows as $r) {
            $cat = CauseCategory::updateOrCreate(['category_code' => $r['l1_code']], ['category_name' => $r['l1'], 'validity' => true]);
            $sub = CauseSubcategory::updateOrCreate(['subcategory_code' => $r['l2_code']], ['cause_category_id' => $cat->id, 'subcategory_name' => $r['l2'], 'validity' => true]);
            $det = CauseDetail::updateOrCreate(['detail_code' => $r['l3_code']], ['cause_subcategory_id' => $sub->id, 'detail_name' => $r['l3'], 'validity' => true]);

            $keep['c'][] = $cat->id; $keep['s'][] = $sub->id; $keep['d'][] = $det->id;
        }

        $this->deactivateOthers(CauseCategory::class, $keep['c']);
        $this->deactivateOthers(CauseSubcategory::class, $keep['s']);
        $this->deactivateOthers(CauseDetail::class, $keep['d']);
    }

    private function impacts(array $rows): void
    {
        $keep = ['c' => [], 'i' => []];

        foreach ($rows as $r) {
            $cat = ImpactCategory::updateOrCreate(['category_code' => $r['l2_code']], ['category_name' => $r['l2'], 'validity' => true]);
            $item = ImpactItem::updateOrCreate(['item_code' => $r['l3_code']], [
                'impact_category_id' => $cat->id, 'item_name' => $r['l3'],
                'nature_code' => $r['nature_code'], 'nature_name' => $r['nature'], 'validity' => true,
            ]);

            $keep['c'][] = $cat->id; $keep['i'][] = $item->id;
        }

        $this->deactivateOthers(ImpactCategory::class, $keep['c']);
        $this->deactivateOthers(ImpactItem::class, $keep['i']);
    }

    private function controls(array $rows): void
    {
        $keep = ['d' => [], 'c' => []];

        foreach ($rows as $r) {
            $domain = ControlDomain::updateOrCreate(['domain_code' => $r['l2_code']], ['domain_name' => $r['l2'], 'validity' => true]);
            $cat = ControlCategory::updateOrCreate(['category_code' => $r['l3_code']], ['control_domain_id' => $domain->id, 'category_name' => $r['l3'], 'validity' => true]);

            $keep['d'][] = $domain->id; $keep['c'][] = $cat->id;
        }

        $this->deactivateOthers(ControlDomain::class, $keep['d']);
        $this->deactivateOthers(ControlCategory::class, $keep['c']);
    }

    /** إلغاء تفعيل أي سجل مش في الملف (بيفضل موجود للمخاطر القديمة) */
    private function deactivateOthers(string $model, array $keepIds): void
    {
        $model::whereNotIn('id', array_values(array_unique($keepIds)))->update(['validity' => false]);
    }
}