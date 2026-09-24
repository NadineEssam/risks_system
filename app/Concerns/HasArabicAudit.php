<?php

namespace App\Concerns;

use Illuminate\Support\Facades\Auth;

/**
 * يملأ تلقائياً أعمدة تتبع من أنشأ/عدّل السجل (created_by / updated_by)
 * باسم المستخدم الحالي، طبقاً لتصميم كل جداول ERD.
 */
trait HasArabicAudit
{
    protected static function bootHasArabicAudit(): void
    {
        static::creating(function ($model) {
            if ($model->isFillable('creation_date') && empty($model->creation_date)) {
                $model->creation_date = now();
            }
            if ($model->isFillable('created_by') && empty($model->created_by)) {
                $model->created_by = Auth::check() ? Auth::user()->name : 'system';
            }
            if ($model->isFillable('updated_by') && empty($model->updated_by)) {
                $model->updated_by = Auth::check() ? Auth::user()->name : 'system';
            }
        });

        static::updating(function ($model) {
            if ($model->isFillable('update_date')) {
                $model->update_date = now();
            }
            if ($model->isFillable('updated_by')) {
                $model->updated_by = Auth::check() ? Auth::user()->name : 'system';
            }
        });
    }
}
