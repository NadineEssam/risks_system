<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePotentialRiskRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('risks.create');
    }

    public function rules(): array
    {
        return [
            // event_type_id / events_id / event_subcategory_id: مطلوبة فقط لأغراض
            // القوائم المنسدلة المتتابعة (cascading dropdowns) في واجهة التسجيل،
            // ولا يتم حفظها على النموذج مباشرة لأنها ليست ضمن fillable الخاص بـ
            // PotentialRiskRegister — القيمة الفعلية المحفوظة هي event_detail_id.
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
            'impact_items'         => ['required', 'array', 'min:1'],
            'impact_items.*'       => ['integer', 'exists:impact_items,id'],
            'control_categories'   => ['nullable', 'array'],
            'control_categories.*' => ['integer', 'exists:control_categories,id'],
            // قبول الخطر (مقبول/غير مقبول) + معالجة الخطر (حل كلي/حل جزئي) — الاتنين مطلوبين
            'acceptance_status_id' => ['required', 'integer',
                \Illuminate\Validation\Rule::exists('resolution_statuses', 'id')->where('status_group', 'acceptance')],
            'treatment_status_id'  => ['required', 'integer',
                \Illuminate\Validation\Rule::exists('resolution_statuses', 'id')->where('status_group', 'treatment')],
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
            'acceptance_status_id.required' => 'يجب تحديد قبول الخطر (مقبول / غير مقبول).',
            'treatment_status_id.required'  => 'يجب تحديد معالجة الخطر (حل كلي / حل جزئي).',
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
    }
}
