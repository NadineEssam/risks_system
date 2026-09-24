<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('incident-followups.create');
    }

    public function rules(): array
    {
        return [
            'incident_id' => ['required', 'integer', 'exists:incidents,id'],
            'followup_entry_type_id' => ['required', 'integer', 'exists:followup_entry_types,id'],
            'followup_status_id' => ['required', 'integer', 'exists:followup_statuses,id'],
            'followup_date' => ['required', 'date', 'before_or_equal:today'],
            'entry_text' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'incident_id.required' => 'يجب اختيار الحدث المراد متابعته.',
            'followup_entry_type_id.required' => 'يجب تحديد تصنيف المتابعة (توصية / رد / رأي).',
            'followup_status_id.required' => 'يجب تحديد حالة المتابعة.',
            'entry_text.required' => 'نص المتابعة مطلوب.',
        ];
    }
}
