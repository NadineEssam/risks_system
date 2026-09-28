<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IndicatorNature;
use App\Models\ThresholdLevel;
use Illuminate\Validation\Validator;

class StoreIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('indicators.create');
    }

    public function rules(): array
    {
        return [
            'potential_risk_register_id' => ['required', 'integer', 'exists:potential_risk_registers,id'],
            'indicator_nature_id' => ['required', 'integer', 'exists:indicator_natures,id'],
            'measurement_unit_id' => ['required', 'integer', 'exists:measurement_units,id'],
            'reporting_frequency_id' => ['required', 'integer', 'exists:reporting_frequencies,id'],
            'activity_unit_id' => ['required', 'integer', 'exists:activity_units,id'],
            'indicator_name' => ['required', 'string'],
            'related_actions' => ['nullable', 'string'],
            'data_sources' => ['nullable', 'string'],

            'thresholds' => ['required', 'array'],
            'thresholds.*.threshold_level_id' => ['required', 'integer', 'exists:threshold_levels,id'],
            'thresholds.*.threshold_value' => ['required', 'numeric'],
            'thresholds.*.required_action' => ['nullable', 'string'],

            'responsibles' => ['required', 'array', 'min:1'],
            'responsibles.*.full_name' => ['required', 'string'],
            'responsibles.*.job_title' => ['nullable', 'string'],
            'responsibles.*.email' => ['nullable', 'email'],
            'responsibles.*.responsible_role_id' => ['required', 'integer', 'exists:responsible_roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'potential_risk_register_id.required' => 'يجب اختيار الخطر المحتمل المرتبط بالمؤشر.',
            'indicator_name.required' => 'اسم/وصف المؤشر مطلوب.',
            'responsibles.required' => 'يجب تعيين مسئول واحد على الأقل للمؤشر.',
        ];
    }
        /**
     * ترتيب الحدود لازم يطابق طبيعة المؤشر:
     * متزايد: مقبول < متوسط < مرتفع — متناقص: مقبول > متوسط > مرتفع
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $nature = IndicatorNature::find($this->input('indicator_nature_id'));
            $rows   = collect($this->input('thresholds', []));
            $levels = ThresholdLevel::whereIn('id', $rows->pluck('threshold_level_id'))->get()->keyBy('id');

            $values = [];
            foreach ($rows as $row) {
                $order = (int) ($levels[$row['threshold_level_id'] ?? 0]->sort_order ?? 0);
                if ($order && is_numeric($row['threshold_value'] ?? null)) {
                    $values[$order] = (float) $row['threshold_value'];
                }
            }

            if (! $nature || count($values) < 3) {
                return;
            }

            $decreasing = trim($nature->nature_name) === 'متناقص';
            $ok = $decreasing
                ? ($values[1] > $values[2] && $values[2] > $values[3])
                : ($values[1] < $values[2] && $values[2] < $values[3]);

            if (! $ok) {
                $validator->errors()->add('thresholds', $decreasing
                    ? 'حدود المؤشر المتناقص يجب أن تكون تنازلية: المقبول أكبر من المتوسط، والمتوسط أكبر من المرتفع.'
                    : 'حدود المؤشر المتزايد يجب أن تكون تصاعدية: المقبول أقل من المتوسط، والمتوسط أقل من المرتفع.');
            }
        });
    }
}
