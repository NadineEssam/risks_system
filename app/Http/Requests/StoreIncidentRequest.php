<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create-incidents');
    }

    public function rules(): array
    {
        return [
            'potential_risk_register_id' => ['required', 'integer', 'exists:potential_risk_registers,id'],
            'discovery_date' => ['required', 'date', 'before_or_equal:today'],
            'start_date' => ['nullable', 'date'],
            'impact_score' => ['required', 'integer', 'min:1', 'max:5'],
            'description' => ['nullable', 'string'],
            'current_procedure' => ['nullable', 'string'],
            'proposed_procedure' => ['nullable', 'string'],
            'actual_impact_problem' => ['nullable', 'string'],
            'responsible_sectors' => ['required', 'array', 'min:1'],
            'responsible_sectors.*' => ['integer', 'exists:new_po.sectors,sec_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'potential_risk_register_id.required' => 'يجب اختيار الخطر المحتمل المرتبط بهذا الحدث.',
            'discovery_date.required' => 'تاريخ اكتشاف الحدث مطلوب.',
            'impact_score.required' => 'درجة الأثر مطلوبة (من 1 إلى 5).',
            'responsible_sectors.required' => 'يجب تحديد قطاع واحد على الأقل مسؤول عن الحل.',
        ];
    }
}
