<?php

namespace App\Http\Requests;

use App\Models\FollowupStatus;
use App\Support\IncidentAccess;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentFollowupRequest extends FormRequest
{
    // الصلاحية بتتفحص من CheckRoutePermission (اسم المسار)
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // إضافة: الحدث من المسار — تعديل: الحدث من المتابعة نفسها
        $current  = $this->route('followup');
        $incident = $this->route('incident') ?? $current?->incidentSectorResponsibility?->incident;

        return [
            'followup_entry_type_id' => ['required', 'integer', 'exists:followup_entry_types,id'],
            'followup_status_id'     => [
                'required', 'integer', 'exists:followup_statuses,id',
                // نفس الشكاوى: الحالة متتكررش على نفس الحدث (ما عدا جارى المتابعة)
                function ($attribute, $value, $fail) use ($incident, $current) {
                    $status = FollowupStatus::find($value);

                    if (! $incident || ! $status || in_array($status->status_name, IncidentAccess::REPEATABLE_STATUSES, true)) {
                        return;
                    }

                    $used = $incident->followups()
                        ->where('followup_status_id', $value)
                        ->when($current, fn ($q) => $q->where('incident_followups.id', '!=', $current->id))
                        ->exists();

                    if ($used) {
                        $fail('حالة المتابعة "'.$status->status_name.'" مستخدمة بالفعل على هذا الحدث.');
                    }
                },
            ],
            'followup_date' => ['required', 'date', 'before_or_equal:today'],
            'entry_text'    => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'followup_entry_type_id.required' => 'يجب تحديد نوع الإدخال (توصية / رد / رأى).',
            'followup_status_id.required'     => 'يجب تحديد حالة المتابعة.',
            'followup_date.required'          => 'تاريخ المتابعة مطلوب.',
            'followup_date.before_or_equal'   => 'تاريخ المتابعة لا يمكن أن يكون في المستقبل.',
            'entry_text.required'             => 'نص المتابعة مطلوب.',
        ];
    }

    public function attributes(): array
    {
        return [
            'followup_entry_type_id' => 'نوع الإدخال',
            'followup_status_id'     => 'حالة المتابعة',
            'followup_date'          => 'تاريخ المتابعة',
            'entry_text'             => 'نص المتابعة',
        ];
    }
}