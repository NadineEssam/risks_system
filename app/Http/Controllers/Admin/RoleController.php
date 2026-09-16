<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * إدارة الأدوار المخصصة ومصفوفة صلاحياتها — بنفس فكرة "نظام خدمة
 * العملاء" (أدوار قابلة للإضافة/التعديل/الحذف، كل دور له مجموعة صلاحيات
 * مُجمَّعة حسب مسار العمل)، لكن بأسلوب الواجهة الموحّد لهذا النظام.
 */
class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create_edit');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRole($request);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', 'تمت إضافة الدور بنجاح.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.create_edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $this->validateRole($request, $role);

        $role->update(['name' => $data['name']]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', 'تم تحديث بيانات الدور بنجاح.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا الدور لأنه مُسنَد لمستخدم واحد أو أكثر.');
        }

        if ($role->name === 'super-admin') {
            return back()->with('error', 'لا يمكن حذف دور مدير النظام.');
        }

        $role->delete();

        return back()->with('success', 'تم حذف الدور بنجاح.');
    }

    protected function validateRole(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required', 'string', 'max:150',
                Rule::unique('roles', 'name')->ignore($role?->id),
            ],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists((new Permission)->getTable(), 'name')],
        ]);
    }
}
