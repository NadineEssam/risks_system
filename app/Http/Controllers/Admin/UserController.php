<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use App\DataTables\UsersDataTable;

class UserController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('admin.users.index', $this->formData());
    }

    public function edit(User $user): View
    {
        $user->load(['roles', 'sector', 'department']);

        return view('admin.users.edit', array_merge($this->formData(), compact('user')));
    }

    /** بيانات الفورمز: الأدوار + القطاعات والإدارات (من new_po) */
    private function formData(): array
    {
        return [
            'roles'       => Role::orderBy('name')->get(),
            'sectors'     => Sector::active()->orderBy('sector_ar')->get(),
            'departments' => Department::active()->orderBy('depname_ar')->get(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // اسم حقل الفورم في الواجهة يفضل "userID" (بدون تغيير في
            // resources/views/admin/users/index.blade.php)، لكن القيمة
            // تُحفَظ على عمود domain_username — راجع ملاحظة إعادة تسمية
            // العمود في الهجرة (تفادياً لعطل الحالة المختلطة مع Oracle).
            'userID' => ['required', 'string', 'max:100', 'unique:users,domain_username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'sector_id' => ['nullable', 'integer', 'exists:new_po.sectors,sec_id'],
            'department_id' => ['nullable', 'integer', 'exists:new_po.departments,dep_id', $this->departmentBelongsToSectorRule($request)],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
        ], [
            'userID.required' => 'حقل اسم مستخدم الدومين مطلوب.',
            'userID.unique' => 'اسم مستخدم الدومين هذا مُسجَّل بالفعل.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'domain_username' => $data['userID'],
            'email' => $data['email'],
            // كلمة المرور هنا لا تُستخدم فعلياً لتسجيل الدخول (المصادقة تتم
            // عبر LDAP بواسطة userID)، لذا تُولَّد تلقائياً وعشوائياً هنا
            // فقط لتوافق عمود password غير القابل للـ NULL على جدول users
            // — لا يوجد أي إدخال لها في الواجهة، ولا داعي لأن يعرفها أحد.
            'password' => Hash::make(Str::random(40)),
            'job_title' => $data['job_title'] ?? null,
            'sector_id' => $data['sector_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'is_active' => true,
        ]);

        $user->syncRoles($data['roles']);

        return back()->with('success', 'تمت إضافة المستخدم بنجاح.');
    }

    public function toggle(User $user): RedirectResponse
    {
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'تم تحديث حالة تفعيل المستخدم.');
    }

    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);

        $user->syncRoles($data['roles']);

        return back()->with('success', 'تم تحديث صلاحيات المستخدم.');
    }

    /**
     * تعديل القطاع/الإدارة لمستخدم موجود بالفعل. أُضيفت هذه الدالة بتاريخ
     * 2026-09-10 لأن `store()` كانت الطريقة الوحيدة لتحديد sector_id/
     * department_id (فقط عند إنشاء مستخدم جديد)، فأي مستخدم قديم (مثل حساب
     * "مدير النظام" المزروع عبر UserSeeder) بلا قطاع/إدارة كان عالقاً بلا أي
     * وسيلة لإصلاح ذلك من الواجهة — وهو ما يمنع تسجيل "الأحداث التشغيلية"
     * (تحقق `IncidentController` يشترط وجود القيمتين معاً).
     */
    public function updateSectorDepartment(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'sector_id' => ['nullable', 'integer', 'exists:new_po.sectors,sec_id'],
            'department_id' => ['nullable', 'integer', 'exists:new_po.departments,dep_id', $this->departmentBelongsToSectorRule($request)],
        ]);

        $user->update([
            'sector_id' => $data['sector_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
        ]);

        return back()->with('success', 'تم تحديث القطاع/الإدارة الخاصة بالمستخدم.');
    }

    /**
     * تأكيد أن الإدارة المختارة تتبع فعلاً القطاع المختار (وليس أي إدارة
     * عشوائية من قائمة كل الإدارات) — تحقق خلفي مكمِّل لقوائم الإدارات
     * المتتابعة (cascading) بالواجهة اللي بتفلتر الإدارات حسب القطاع
     * تلقائياً بجافاسكريبت؛ هذا التحقق يمنع إرسال قيمة غير متطابقة يدوياً.
     */
    private function departmentBelongsToSectorRule(Request $request): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($request) {
            if (! $value || ! $request->filled('sector_id')) {
                return;
            }

            $sector = Sector::find($request->input('sector_id'));
            $department = Department::find($value);

            if ($sector && $department && $department->sector_code !== $sector->sector_code) {
                $fail('الإدارة المختارة لا تتبع القطاع المحدد.');
            }
        };
    }
}