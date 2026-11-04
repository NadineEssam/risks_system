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
            'cause_detail_id'      => ['required', 'integer', 'exists:cause_details,id'],
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
            'cause_detail_id.required' => 'يجب اختيار السبب.',
            'impact_items.required' => 'يجب اختيار أثر واحد على الأقل.',
        ];
    }
}
