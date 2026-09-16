<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventDetailController;
use App\Http\Controllers\Admin\EventSubcategoryController;
use App\Http\Controllers\Admin\LookupController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SectorController;
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

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ================= المرحلة الأولى: سجل المخاطر المحتملة =================
    Route::middleware('permission:view-risks')->group(function () {
        Route::get('/risks', [PotentialRiskRegisterController::class, 'index'])->name('risks.index');
        Route::get('/risks/{risk}', [PotentialRiskRegisterController::class, 'show'])->name('risks.show');
    });
    Route::middleware('permission:create-risks')->group(function () {
        Route::get('/risks-create', [PotentialRiskRegisterController::class, 'create'])->name('risks.create');
        Route::post('/risks', [PotentialRiskRegisterController::class, 'store'])->name('risks.store');
    });
    Route::middleware('permission:edit-risks')->group(function () {
        Route::get('/risks/{risk}/edit', [PotentialRiskRegisterController::class, 'edit'])->name('risks.edit');
        Route::put('/risks/{risk}', [PotentialRiskRegisterController::class, 'update'])->name('risks.update');
        Route::post('/risks/{risk}/toggle', [PotentialRiskRegisterController::class, 'toggle'])->name('risks.toggle');
        Route::post('/risks/{risk}/sectors', [PotentialRiskRegisterSectorController::class, 'store'])->name('risks.sectors.store');
        Route::delete('/risks/{risk}/sectors/{sectorDetail}', [PotentialRiskRegisterSectorController::class, 'destroy'])->name('risks.sectors.destroy');
        Route::post('/risk-sector-details/{sectorDetail}/actions', [RequiredActionController::class, 'store'])->name('risk-sectors.actions.store');
        Route::delete('/required-actions/{requiredAction}', [RequiredActionController::class, 'destroy'])->name('required-actions.destroy');
    });
    Route::middleware('permission:approve-risks')->group(function () {
        Route::post('/risks/{risk}/status', [PotentialRiskRegisterController::class, 'updateStatus'])->name('risks.status.update');
    });

    // ======================= المرحلة الثانية: الحدث =======================
    Route::middleware('permission:view-incidents')->group(function () {
        Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
        Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    });
    Route::middleware('permission:create-incidents')->group(function () {
        Route::get('/incidents-create', [IncidentController::class, 'create'])->name('incidents.create');
        Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    });
    Route::middleware('permission:edit-incidents')->group(function () {
        Route::post('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.status.update');
    });

    // ==================== المرحلة الثالثة: متابعة الحدث ====================
    Route::middleware('permission:view-incident-followups')->group(function () {
        Route::get('/incident-followups', [IncidentFollowupController::class, 'index'])->name('incident-followups.index');
    });
    Route::middleware('permission:create-incident-followups')->group(function () {
        Route::get('/incident-followups-create', [IncidentFollowupController::class, 'create'])->name('incident-followups.create');
        Route::post('/incident-followups', [IncidentFollowupController::class, 'store'])->name('incident-followups.store');
    });

    // ================= المرحلة الرابعة: المؤشر (KRI) =================
    Route::middleware('permission:view-indicators')->group(function () {
        Route::get('/indicators', [IndicatorController::class, 'index'])->name('indicators.index');
        Route::get('/indicators/{indicator}', [IndicatorController::class, 'show'])->name('indicators.show');
    });
    Route::middleware('permission:create-indicators')->group(function () {
        Route::get('/indicators-create', [IndicatorController::class, 'create'])->name('indicators.create');
        Route::post('/indicators', [IndicatorController::class, 'store'])->name('indicators.store');
    });

    // ================= المرحلة الخامسة: متابعة المؤشر =================
    Route::middleware('permission:view-indicator-followups')->group(function () {
        Route::get('/indicator-followups', [IndicatorFollowupController::class, 'index'])->name('indicator-followups.index');
    });
    Route::middleware('permission:create-indicator-followups')->group(function () {
        Route::get('/indicator-followups-create', [IndicatorFollowupController::class, 'create'])->name('indicator-followups.create');
        Route::post('/indicator-followups', [IndicatorFollowupController::class, 'store'])->name('indicator-followups.store');
    });

    // ============================= التقارير =============================
    Route::middleware('permission:view-reports')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // ===================== الإدارة والبيانات المرجعية =====================
    Route::middleware('permission:manage-lookups')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/sectors', [SectorController::class, 'index'])->name('sectors.index');
        Route::post('/sectors', [SectorController::class, 'store'])->name('sectors.store');
        Route::post('/sectors/{sector}/toggle', [SectorController::class, 'toggle'])->name('sectors.toggle');

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::post('/departments/{department}/toggle', [DepartmentController::class, 'toggle'])->name('departments.toggle');

        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::post('/events/{event}/toggle', [EventController::class, 'toggle'])->name('events.toggle');

        Route::get('/event-subcategories', [EventSubcategoryController::class, 'index'])->name('event-subcategories.index');
        Route::post('/event-subcategories', [EventSubcategoryController::class, 'store'])->name('event-subcategories.store');
        Route::post('/event-subcategories/{eventSubcategory}/toggle', [EventSubcategoryController::class, 'toggle'])->name('event-subcategories.toggle');

        Route::get('/event-details', [EventDetailController::class, 'index'])->name('event-details.index');
        Route::post('/event-details', [EventDetailController::class, 'store'])->name('event-details.store');
        Route::post('/event-details/{eventDetail}/toggle', [EventDetailController::class, 'toggle'])->name('event-details.toggle');

        // البيانات المرجعية أحادية العمود (Lookups) — متحكم عام موحّد
        Route::post('/lookups/{type}', [LookupController::class, 'store'])->name('lookups.store');
        Route::post('/lookups/{type}/{id}/toggle', [LookupController::class, 'toggle'])->name('lookups.toggle');

        foreach (array_keys(LookupRegistry::definitions()) as $slug) {
            Route::get("/{$slug}", fn (LookupController $controller) => $controller->index($slug))
                ->name("{$slug}.index");
        }
    });

    Route::middleware('permission:manage-users')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update');
        Route::post('/users/{user}/sector-department', [UserController::class, 'updateSectorDepartment'])->name('users.sectorDepartment.update');

        // إدارة الأدوار (الأدوار المخصصة + مصفوفة الصلاحيات) — نفس فكرة
        // "نظام خدمة العملاء" لكن بأسلوب الواجهة الموحّد لهذا النظام.
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});
