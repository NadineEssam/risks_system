<?php

namespace App\Http\Requests;

use App\Models\ThresholdLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreIndicatorFollowupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('indicator-followups.create');
    }

    public function rules(): array
    {
        return [
            'indicators_id' => ['required', 'integer', 'exists:indicators,id'],
            'threshold_level_id' => ['required', 'integer', 'exists:threshold_levels,id'],
            'measurement_date' => ['required', 'date', 'before_or_equal:today'],
            'actual_value' => ['required', 'numeric'],
            'change_reason' => ['nullable', 'string'],
            'action_taken' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'indicators_id.required' => 'يجب اختيار المؤشر المراد متابعته.',
            'threshold_level_id.required' => 'يجب تحديد مستوى حد المؤشر.',
            'actual_value.required' => 'القيمة الفعلية مطلوبة.',
        ];
    }

    /**
     * إلزامية "أسباب التغيّر" و"الإجراء المتخذ" إذا كان مستوى الحد
     * لا يساوي "مقبول"، طبقاً لخطوة 5 من مسار متابعة المؤشر.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $levelId = $this->input('threshold_level_id');

            if (! $levelId) {
                return;
            }

            $level = ThresholdLevel::find($levelId);

            if ($level && ! $level->isAcceptable()) {
                if (blank($this->input('change_reason'))) {
                    $validator->errors()->add('change_reason', 'يجب تسجيل أسباب التغيّر عندما لا يكون مستوى الحد مقبولاً.');
                }
                if (blank($this->input('action_taken'))) {
                    $validator->errors()->add('action_taken', 'يجب تسجيل الإجراء المتخذ عندما لا يكون مستوى الحد مقبولاً.');
                }
            }
        });
    }
}
