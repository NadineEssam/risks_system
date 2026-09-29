<?php


// use App\Http\Controllers\Admin\EventController;
// use App\Http\Controllers\Admin\EventDetailController;
// use App\Http\Controllers\Admin\EventSubcategoryController;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentFollowupController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\IndicatorFollowupController;
use App\Http\Controllers\PotentialRiskRegisterController;
use App\Http\Controllers\PotentialRiskRegisterSectorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequiredActionController;
use App\Support\LookupRegistry;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| مسارات النظام
|--------------------------------------------------------------------------
| كل شاشات النظام باللغة العربية. المسارات مقسّمة حسب المراحل الخمس
| الموضحة في وثيقة المتطلبات (سجل المخاطر، الحدث، متابعة الحدث، المؤشر،
| متابعة المؤشر) بالإضافة إلى شاشات الإدارة والتقارير.
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware(['auth', 'route.permission'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ================= المرحلة الأولى: سجل المخاطر المحتملة =================
    Route::get('/risks', [PotentialRiskRegisterController::class, 'index'])->name('risks.index');
    Route::get('/risks-create', [PotentialRiskRegisterController::class, 'create'])->name('risks.create');
    Route::post('/risks', [PotentialRiskRegisterController::class, 'store'])->name('risks.store');
    Route::get('/risks/{risk}', [PotentialRiskRegisterController::class, 'show'])->name('risks.show');
    Route::get('/risks/{risk}/edit', [PotentialRiskRegisterController::class, 'edit'])->name('risks.edit');
    Route::put('/risks/{risk}', [PotentialRiskRegisterController::class, 'update'])->name('risks.update');
    Route::post('/risks/{risk}/toggle', [PotentialRiskRegisterController::class, 'toggle'])->name('risks.toggle');
    Route::post('/risks/{risk}/status', [PotentialRiskRegisterController::class, 'updateStatus'])->name('risks.status.update');
    Route::post('/risks/{risk}/sectors', [PotentialRiskRegisterSectorController::class, 'store'])->name('risks.sectors.store');
    Route::delete('/risks/{risk}/sectors/{sectorDetail}', [PotentialRiskRegisterSectorController::class, 'destroy'])->name('risks.sectors.destroy');
    Route::post('/risk-sector-details/{sectorDetail}/actions', [RequiredActionController::class, 'store'])->name('risk-sectors.actions.store');
    Route::delete('/required-actions/{requiredAction}', [RequiredActionController::class, 'destroy'])->name('required-actions.destroy');

    // ======================= المرحلة الثانية: الحدث =======================
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents-create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::post('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.status.update');

    // ======= المرحلة الثالثة: متابعة الحدث (نفس "الرد على البيان" في الشكاوى) =======
    // سجل متابعات حدث معيّن
    Route::get('/incidents/{incident}/followups', [IncidentFollowupController::class, 'index'])->name('incident-followups.index');
    Route::get('/incidents/{incident}/followups/create', [IncidentFollowupController::class, 'create'])->name('incident-followups.create');
    Route::post('/incidents/{incident}/followups', [IncidentFollowupController::class, 'store'])->name('incident-followups.store');
    // متابعة واحدة
    Route::get('/incident-followups/{followup}', [IncidentFollowupController::class, 'show'])->name('incident-followups.show');
    Route::get('/incident-followups/{followup}/edit', [IncidentFollowupController::class, 'edit'])->name('incident-followups.edit');
    Route::put('/incident-followups/{followup}', [IncidentFollowupController::class, 'update'])->name('incident-followups.update');
    Route::delete('/incident-followups/{followup}', [IncidentFollowupController::class, 'destroy'])->name('incident-followups.destroy');

    // ================= المرحلة الرابعة: المؤشر (KRI) =================
    Route::get('/indicators', [IndicatorController::class, 'index'])->name('indicators.index');
    Route::get('/indicators-create', [IndicatorController::class, 'create'])->name('indicators.create');
    Route::post('/indicators', [IndicatorController::class, 'store'])->name('indicators.store');
    Route::get('/indicators/{indicator}', [IndicatorController::class, 'show'])->name('indicators.show');
    Route::get('/indicators/{indicator}/edit', [IndicatorController::class, 'edit'])->name('indicators.edit');
    Route::put('/indicators/{indicator}', [IndicatorController::class, 'update'])->name('indicators.update');

    // ================= المرحلة الخامسة: متابعة المؤشر =================
    // ========= المرحلة الخامسة: متابعة المؤشر (نفس متابعة الحدث) =========
    // سجل قياسات مؤشر معيّن
    Route::get('/indicators/{indicator}/followups', [IndicatorFollowupController::class, 'index'])->name('indicator-followups.index');
    Route::get('/indicators/{indicator}/followups/create', [IndicatorFollowupController::class, 'create'])->name('indicator-followups.create');
    Route::post('/indicators/{indicator}/followups', [IndicatorFollowupController::class, 'store'])->name('indicator-followups.store');
    // قياس واحد
    Route::get('/indicator-followups/{followup}', [IndicatorFollowupController::class, 'show'])->name('indicator-followups.show');
    Route::get('/indicator-followups/{followup}/edit', [IndicatorFollowupController::class, 'edit'])->name('indicator-followups.edit');
    Route::put('/indicator-followups/{followup}', [IndicatorFollowupController::class, 'update'])->name('indicator-followups.update');
    Route::delete('/indicator-followups/{followup}', [IndicatorFollowupController::class, 'destroy'])->name('indicator-followups.destroy');

    // ============================= التقارير =============================
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // ===================== الإدارة والبيانات المرجعية =====================
    Route::prefix('admin')->name('admin.')->group(function () {
        // ===== البيانات المرجعية (Lookups) + تصنيف بازل — CRUD عام واحد للكل =====
        // كل slug من LookupRegistry بياخد: index / create / store / show / edit / update / destroy
        // (create قبل {id} عشان "create" متتقريش كـ id)
        foreach (array_keys(LookupRegistry::definitions()) as $slug) {
            Route::prefix($slug)->name("{$slug}.")->group(function () use ($slug) {
                Route::get('/', [LookupController::class, 'index'])->name('index')->defaults('type', $slug);
                Route::get('/create', [LookupController::class, 'create'])->name('create')->defaults('type', $slug);
                Route::post('/', [LookupController::class, 'store'])->name('store')->defaults('type', $slug);
                Route::get('/{id}', [LookupController::class, 'show'])->name('show')->defaults('type', $slug);
                Route::get('/{id}/edit', [LookupController::class, 'edit'])->name('edit')->defaults('type', $slug);
                Route::put('/{id}', [LookupController::class, 'update'])->name('update')->defaults('type', $slug);
                Route::delete('/{id}', [LookupController::class, 'destroy'])->name('destroy')->defaults('type', $slug);
            });
        }

        // المستخدمون
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
        Route::post('/users/{user}/sector-department', [UserController::class, 'updateSectorDepartment'])->name('users.sectorDepartment.update');

        // الأدوار والصلاحيات
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});
