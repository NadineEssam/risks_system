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
            'resolution_status_id' => ['required', 'integer', 'exists:resolution_statuses,id'],
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
            'resolution_status_id.required' => 'يجب تحديد الحالة الأولية للخطر المحتمل.',
        ];
    }
}
