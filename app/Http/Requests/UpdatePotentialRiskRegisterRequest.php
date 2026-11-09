<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePotentialRiskRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('risks.edit');
    }

    public function rules(): array
    {
        return [
            'event_type_id' => ['required', 'integer', 'exists:event_types,id'],
            'events_id' => ['required', 'integer', 'exists:events,id'],
            'event_subcategory_id' => ['required', 'integer', 'exists:event_subcategories,id'],
            'event_detail_id' => ['required', 'integer', 'exists:event_details,id'],
            'risk_description' => ['required', 'string'],
            'proposed_control' => ['nullable', 'string'],
            // تصنيفات ERM: السبب واحد — الأثر واحد أو أكتر — الضوابط اختيارية
            // الأسباب: سبب واحد أو أكتر، من غير تكرار، ولكل سبب نسبة
            'causes'                   => ['required', 'array', 'min:1'],
            'causes.*.cause_detail_id' => ['required', 'integer', 'distinct', 'exists:cause_details,id'],
            'causes.*.percentage'      => ['required', 'numeric', 'min:0.01', 'max:100'],
            // شهية المخاطر: 3 مستويات، كل حد من 1 لـ 25
            'appetite'                      => ['required', 'array', 'min:3'],
            'appetite.*.threshold_level_id' => ['required', 'integer', 'distinct', 'exists:threshold_levels,id'],
            'appetite.*.threshold_value'    => ['required', 'integer', 'min:1', 'max:25'],
            'appetite.*.required_action'    => ['nullable', 'string'],
            'impact_items'         => ['required', 'array', 'min:1'],
            'impact_items.*'       => ['integer', 'exists:impact_items,id'],
            'control_categories'   => ['nullable', 'array'],
            'control_categories.*' => ['integer', 'exists:control_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_type_id.required' => 'يجب اختيار تصنيف بازل العام.',
            'events_id.required' => 'يجب اختيار تصنيف بازل التفصيلي.',
            'event_subcategory_id.required' => 'يجب اختيار تصنيف بازل الفرعي.',
            'event_detail_id.required' => 'يجب اختيار تصنيف بازل الدقيق.',
            'risk_description.required' => 'وصف الخطر المحتمل مطلوب.',
            'causes.required'                   => 'يجب إضافة سبب واحد على الأقل.',
            'causes.*.cause_detail_id.required' => 'يجب اختيار السبب.',
            'causes.*.cause_detail_id.distinct' => 'لا يمكن تكرار نفس السبب.',
            'causes.*.percentage.required'      => 'يجب إدخال نسبة السبب.',
            'impact_items.required' => 'يجب اختيار أثر واحد على الأقل.',
        ];
    }
        /** مجموع نسب الأسباب لازم يساوي 100% */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $v) {
            if (! is_array($this->input('causes'))) {
                return;
            }

            $total = round(collect($this->input('causes'))->sum(fn ($c) => (float) ($c['percentage'] ?? 0)), 2);

            if ($total !== 100.0) {
                $v->errors()->add('causes', "مجموع نسب الأسباب يجب أن يساوي 100% (المجموع الحالي {$total}%).");
            }
        });

        // شهية المخاطر: لازم تكون تصاعدية (المقبول < المتوسط < المرتفع)
        $validator->after(function (\Illuminate\Validation\Validator $v) {
            $rows = collect($this->input('appetite', []));
            if ($rows->isEmpty()) {
                return;
            }

            $order = \App\Models\ThresholdLevel::whereIn('id', $rows->pluck('threshold_level_id'))->pluck('sort_order', 'id');
            $values = $rows->mapWithKeys(fn ($r) => [
                (int) ($order[$r['threshold_level_id'] ?? 0] ?? 0) => (int) ($r['threshold_value'] ?? 0),
            ]);

            if (isset($values[1], $values[2], $values[3]) && ! ($values[1] < $values[2] && $values[2] < $values[3])) {
                $v->errors()->add('appetite', 'حدود شهية المخاطر يجب أن تكون تصاعدية: المقبول أقل من المتوسط، والمتوسط أقل من المرتفع.');
            }
        });
    }
}
