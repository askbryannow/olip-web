<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortalAuthController;
use App\Livewire\Blip;
use App\Livewire\Dashboard;
use App\Livewire\Entry;
use App\Livewire\Portal;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::middleware('admin.session')->group(function () {
    Route::get('/', Dashboard\ExecutiveOverview::class)->name('dashboard.exec');
    Route::get('/business-performance', Dashboard\BusinessPerformance::class)->name('dashboard.business');
    Route::get('/recruitment', Dashboard\Recruitment::class)->name('dashboard.recruitment');
    Route::get('/turnover', Dashboard\Turnover::class)->name('dashboard.turnover');
    Route::get('/payroll', Dashboard\Payroll::class)->name('dashboard.payroll');
    Route::get('/attendance', Dashboard\Attendance::class)->name('dashboard.attendance');
    Route::get('/learning', Dashboard\Learning::class)->name('dashboard.learning');
    Route::get('/engagement', Dashboard\Engagement::class)->name('dashboard.engagement');
    Route::get('/safety', Dashboard\Safety::class)->name('dashboard.safety');

    Route::prefix('data-entry')->name('entry.')->group(function () {
        Route::get('/', Entry\Checklist::class)->name('checklist');
        Route::get('/employees', Entry\Employees::class)->name('employees');
        Route::get('/outlets', Entry\Outlets::class)->name('outlets');
        Route::get('/overtime', Entry\Overtime::class)->name('overtime');
        Route::get('/attendance', Entry\Attendance::class)->name('attendance');
        Route::get('/recruitment', Entry\Recruitment::class)->name('recruitment');
        Route::get('/training', Entry\Training::class)->name('training');
        Route::get('/engagement', Entry\Engagement::class)->name('engagement');
        Route::get('/incidents', Entry\Incidents::class)->name('incidents');
        Route::get('/business-performance', Entry\Business::class)->name('business');
    });

    Route::prefix('blip')->name('blip.')->group(function () {
        Route::get('/registrations', Blip\Registrations::class)->name('registrations');
        Route::get('/cohorts', Blip\Cohorts::class)->name('cohorts');
        Route::get('/stamps', Blip\Stamps::class)->name('stamps');
        Route::get('/pipeline', Blip\Pipeline::class)->name('pipeline');
    });
});

// BLIP employee portal — separate login, separate "employee" guard, for individual staff.
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/register', Portal\Register::class)->name('register');
    Route::get('/login', [PortalAuthController::class, 'show'])->name('login');
    Route::post('/login', [PortalAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [PortalAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:employee')->group(function () {
        Route::get('/', Portal\Passport::class)->name('passport');
        Route::get('/enroll', Portal\Enroll::class)->name('enroll');
    });
});
