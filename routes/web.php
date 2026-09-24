<?php


use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventDetailController;
use App\Http\Controllers\Admin\EventSubcategoryController;
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

    // ==================== المرحلة الثالثة: متابعة الحدث ====================
    Route::get('/incident-followups', [IncidentFollowupController::class, 'index'])->name('incident-followups.index');
    Route::get('/incident-followups-create', [IncidentFollowupController::class, 'create'])->name('incident-followups.create');
    Route::post('/incident-followups', [IncidentFollowupController::class, 'store'])->name('incident-followups.store');

    // ================= المرحلة الرابعة: المؤشر (KRI) =================
    Route::get('/indicators', [IndicatorController::class, 'index'])->name('indicators.index');
    Route::get('/indicators-create', [IndicatorController::class, 'create'])->name('indicators.create');
    Route::post('/indicators', [IndicatorController::class, 'store'])->name('indicators.store');
    Route::get('/indicators/{indicator}', [IndicatorController::class, 'show'])->name('indicators.show');

    // ================= المرحلة الخامسة: متابعة المؤشر =================
    Route::get('/indicator-followups', [IndicatorFollowupController::class, 'index'])->name('indicator-followups.index');
    Route::get('/indicator-followups-create', [IndicatorFollowupController::class, 'create'])->name('indicator-followups.create');
    Route::post('/indicator-followups', [IndicatorFollowupController::class, 'store'])->name('indicator-followups.store');

    // ============================= التقارير =============================
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // ===================== الإدارة والبيانات المرجعية =====================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::post('/events/{event}/toggle', [EventController::class, 'toggle'])->name('events.toggle');

        Route::get('/event-subcategories', [EventSubcategoryController::class, 'index'])->name('event-subcategories.index');
        Route::post('/event-subcategories', [EventSubcategoryController::class, 'store'])->name('event-subcategories.store');
        Route::post('/event-subcategories/{eventSubcategory}/toggle', [EventSubcategoryController::class, 'toggle'])->name('event-subcategories.toggle');

        Route::get('/event-details', [EventDetailController::class, 'index'])->name('event-details.index');
        Route::post('/event-details', [EventDetailController::class, 'store'])->name('event-details.store');
        Route::post('/event-details/{eventDetail}/toggle', [EventDetailController::class, 'toggle'])->name('event-details.toggle');

        // البيانات المرجعية أحادية العمود (Lookups)
        Route::post('/lookups/{type}', [LookupController::class, 'store'])->name('lookups.store');
        Route::post('/lookups/{type}/{id}/toggle', [LookupController::class, 'toggle'])->name('lookups.toggle');

        foreach (array_keys(LookupRegistry::definitions()) as $slug) {
            Route::get("/{$slug}", fn (LookupController $controller) => $controller->index($slug))
                ->name("{$slug}.index");
        }

        // المستخدمون
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
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
