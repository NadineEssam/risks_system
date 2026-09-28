<?php

// رسائل التحقق بالعربي — :attribute بيتبدل باسم الحقل العربي من 'attributes' تحت
return [
    'accepted'        => 'يجب قبول حقل :attribute.',
    'after'           => 'حقل :attribute يجب أن يكون تاريخاً بعد :date.',
    'after_or_equal'  => 'حقل :attribute يجب أن يكون تاريخاً يساوي أو يلي :date.',
    'array'           => 'حقل :attribute يجب أن يكون قائمة.',
    'before'          => 'حقل :attribute يجب أن يكون تاريخاً قبل :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخاً يساوي أو يسبق :date.',
    'between'         => [
        'numeric' => 'حقل :attribute يجب أن يكون بين :min و :max.',
        'string'  => 'حقل :attribute يجب أن يكون بين :min و :max حرفاً.',
        'array'   => 'حقل :attribute يجب أن يحتوي بين :min و :max عناصر.',
    ],
    'boolean'         => 'حقل :attribute يجب أن يكون نعم أو لا.',
    'confirmed'       => 'تأكيد حقل :attribute غير مطابق.',
    'date'            => 'حقل :attribute ليس تاريخاً صحيحاً.',
    'date_format'     => 'حقل :attribute لا يطابق الصيغة :format.',
    'different'       => 'حقل :attribute يجب أن يكون مختلفاً عن :other.',
    'digits'          => 'حقل :attribute يجب أن يكون :digits أرقام.',
    'distinct'        => 'حقل :attribute به قيمة مكررة.',
    'email'           => 'حقل :attribute يجب أن يكون بريداً إلكترونياً صحيحاً.',
    'exists'          => 'القيمة المختارة في حقل :attribute غير صحيحة.',
    'gt'              => ['numeric' => 'حقل :attribute يجب أن يكون أكبر من :value.'],
    'gte'             => ['numeric' => 'حقل :attribute يجب أن يكون أكبر من أو يساوي :value.'],
    'in'              => 'القيمة المختارة في حقل :attribute غير صحيحة.',
    'integer'         => 'حقل :attribute يجب أن يكون رقماً صحيحاً.',
    'lt'              => ['numeric' => 'حقل :attribute يجب أن يكون أقل من :value.'],
    'lte'             => ['numeric' => 'حقل :attribute يجب أن يكون أقل من أو يساوي :value.'],
    'max'             => [
        'numeric' => 'حقل :attribute يجب ألا يزيد عن :max.',
        'string'  => 'حقل :attribute يجب ألا يزيد عن :max حرفاً.',
        'array'   => 'حقل :attribute يجب ألا يحتوي أكثر من :max عناصر.',
    ],
    'min'             => [
        'numeric' => 'حقل :attribute يجب ألا يقل عن :min.',
        'string'  => 'حقل :attribute يجب ألا يقل عن :min أحرف.',
        'array'   => 'حقل :attribute يجب أن يحتوي :min عنصر على الأقل.',
    ],
    'not_in'          => 'القيمة المختارة في حقل :attribute غير صحيحة.',
    'numeric'         => 'حقل :attribute يجب أن يكون رقماً.',
    'present'         => 'حقل :attribute يجب أن يكون موجوداً.',
    'required'        => 'حقل :attribute مطلوب.',
    'required_if'     => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_with'   => 'حقل :attribute مطلوب عند وجود :values.',
    'same'            => 'حقل :attribute يجب أن يطابق :other.',
    'size'            => [
        'numeric' => 'حقل :attribute يجب أن يساوي :size.',
        'string'  => 'حقل :attribute يجب أن يكون :size حرفاً.',
    ],
    'string'          => 'حقل :attribute يجب أن يكون نصاً.',
    'unique'          => 'قيمة حقل :attribute مستخدمة من قبل.',

    // "today" في رسائل التواريخ تظهر "اليوم"
    'values' => [
        'discovery_date'   => ['today' => 'اليوم'],
        'followup_date'    => ['today' => 'اليوم'],
        'measurement_date' => ['today' => 'اليوم'],
        'start_date'       => ['today' => 'اليوم'],
    ],

    // أسماء الحقول بالعربي
    'attributes' => [
        // سجل المخاطر
        'risk_description'     => 'وصف الخطر',
        'proposed_control'     => 'الضابط الرقابي المقترح',
        'event_type_id'        => 'تصنيف بازل العام',
        'events_id'            => 'تصنيف بازل التفصيلي',
        'event_subcategory_id' => 'تصنيف بازل الفرعي',
        'event_detail_id'      => 'تصنيف بازل الدقيق',
        'resolution_status_id' => 'حالة حل الخطر',
        'sectors_sec_id'       => 'القطاع',
        'related_actions'      => 'الإجراءات المرتبطة',

        // الحدث
        'potential_risk_register_id' => 'الخطر المحتمل',
        'discovery_date'             => 'تاريخ الاكتشاف',
        'frequency_score'            => 'درجة التكرار',
        'impact_score'               => 'درجة الأثر',
        'description'                => 'وصف الحدث',
        'current_procedure'          => 'الإجراء الحالي',
        'proposed_procedure'         => 'الإجراء المقترح',
        'actual_impact_problem'      => 'الأثر الفعلي للمشكلة',
        'responsible_sectors'        => 'القطاعات المسؤولة',
        'responsible_sectors.*'      => 'القطاع المسؤول',

        // متابعة الحدث
        'incident_id'            => 'الحدث',
        'followup_date'          => 'تاريخ المتابعة',
        'followup_entry_type_id' => 'نوع الإدخال',
        'followup_status_id'     => 'حالة المتابعة',
        'entry_text'             => 'نص المتابعة',

        // المؤشر
        'indicator_name'                => 'اسم المؤشر',
        'indicator_nature_id'           => 'طبيعة المؤشر',
        'measurement_unit_id'           => 'وحدة القياس',
        'reporting_frequency_id'        => 'دورية الإبلاغ',
        'activity_unit_id'              => 'وحدة النشاط',
        'data_sources'                  => 'مصادر البيانات',
        'start_date'                    => 'تاريخ البدء',
        'notes'                         => 'الملاحظات',
        'thresholds'                    => 'حدود المؤشر',
        'thresholds.*.threshold_level_id' => 'مستوى الحد',
        'thresholds.*.threshold_value'  => 'قيمة الحد',
        'thresholds.*.required_action'  => 'الإجراء المطلوب',
        'responsibles'                  => 'المسئولون',
        'responsibles.*.full_name'      => 'اسم المسئول',
        'responsibles.*.job_title'      => 'المسمى الوظيفي للمسئول',
        'responsibles.*.email'          => 'البريد الإلكتروني للمسئول',
        'responsibles.*.responsible_role_id' => 'دور المسئول',

        // متابعة المؤشر
        'indicators_id'      => 'المؤشر',
        'measurement_date'   => 'تاريخ القياس',
        'actual_value'       => 'القيمة الفعلية',
        'threshold_level_id' => 'مستوى حد الخطر',
        'change_reason'      => 'أسباب التغيّر',
        'action_taken'       => 'الإجراء المتخذ',

        // المستخدمون والأدوار
        'name'          => 'الاسم',
        'userID'        => 'اسم مستخدم الدومين',
        'username'      => 'اسم المستخدم',
        'email'         => 'البريد الإلكتروني',
        'password'      => 'كلمة المرور',
        'job_title'     => 'المسمى الوظيفي',
        'sector_id'     => 'القطاع',
        'department_id' => 'الإدارة',
        'roles'         => 'الأدوار',
        'permissions'   => 'الصلاحيات',
    ],
];