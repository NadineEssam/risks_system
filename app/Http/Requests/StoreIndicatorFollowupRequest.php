<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreIndicatorFollowupRequest extends FormRequest
{
    // الصلاحية بتتفحص من CheckRoutePermission (اسم المسار)
    public function authorize(): bool
    {
        return true;
    }

    /** المؤشر: من المسار (إضافة) أو من القياس نفسه (تعديل) */
    protected function indicator()
    {
        return $this->route('indicator') ?? $this->route('followup')?->indicator;
    }

    public function rules(): array
    {
        // threshold_level_id مش مطلوب من الفورم — بيتحسب تلقائياً
        return [
            'measurement_date' => ['required', 'date', 'before_or_equal:today'],
            'actual_value'     => ['required', 'numeric'],
            'change_reason'    => ['nullable', 'string'],
            'action_taken'     => ['nullable', 'string'],
            'notes'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'measurement_date.required'        => 'تاريخ القياس مطلوب.',
            'measurement_date.before_or_equal' => 'تاريخ القياس لا يمكن أن يكون في المستقبل.',
            'actual_value.required'            => 'القيمة الفعلية مطلوبة.',
            'actual_value.numeric'             => 'القيمة الفعلية يجب أن تكون رقماً.',
        ];
    }

    /**
     * بعد الحساب التلقائي للمستوى:
     * - لو حدود المؤشر ناقصة → خطأ واضح
     * - لو المستوى مش "مقبول" → أسباب التغيّر والإجراء المتخذ إلزاميين
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $indicator = $this->indicator();
            $value     = $this->input('actual_value');

            if (! $indicator || ! is_numeric($value)) {
                return;
            }

            $level = $indicator->calculateThresholdLevel($value);

            if (! $level) {
                $validator->errors()->add('actual_value', 'لا يمكن حساب مستوى حد الخطر: حدود المؤشر (المتوسط والمرتفع) غير مكتملة.');

                return;
            }

            if (! $level->isAcceptable()) {
                if (blank($this->input('change_reason'))) {
                    $validator->errors()->add('change_reason', 'يجب تسجيل أسباب التغيّر لأن المستوى "'.$level->level_name.'".');
                }
                if (blank($this->input('action_taken'))) {
                    $validator->errors()->add('action_taken', 'يجب تسجيل الإجراء المتخذ لأن المستوى "'.$level->level_name.'".');
                }
            }
        });
    }
}