<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $academicYear = \App\Models\Setting::where('key', 'academic_year')->first()?->value ?? date('Y');
    $totalRegistrants = \App\Models\Registration::where('academic_year', $academicYear)->count();
    $byInterest = \App\Models\Registration::selectRaw('JSON_UNQUOTE(JSON_EXTRACT(additional_data, "$.major")) as major, count(*) as count')
        ->where('academic_year', $academicYear)
        ->groupBy('major')
        ->get();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'totalRegistrants' => $totalRegistrants,
        'byInterest' => $byInterest,
    ]);
})->name('home');

// Student Registration Routes (No Auth)
Route::prefix('pendaftaran')->group(function () {
    Route::get('/', [RegistrationController::class, 'start'])->name('register.start');
    Route::post('/registrasi-awal', [RegistrationController::class, 'storeInitial'])->name('register.storeInitial');
    
    Route::get('/formulir/{number}/{code}', [RegistrationController::class, 'showForm'])->name('register.form');
    Route::post('/simpan-biodata', [RegistrationController::class, 'saveBiodata'])->name('register.saveBiodata');
    Route::post('/simpan-ortu', [RegistrationController::class, 'saveParent'])->name('register.saveParent');
    Route::post('/simpan-nilai', [RegistrationController::class, 'saveGrades'])->name('register.saveGrades');
    Route::post('/simpan-bagian', [RegistrationController::class, 'saveSection'])->name('register.saveSection');
    Route::post('/upload-dokumen', [RegistrationController::class, 'uploadDocument'])->name('register.uploadDocument');
    Route::delete('/dokumen/{id}', [RegistrationController::class, 'deleteDocument'])->name('register.deleteDocument');
    Route::post('/finalisasi', [RegistrationController::class, 'finalize'])->name('register.finalize');
});

Route::get('/cek-status', [RegistrationController::class, 'checkStatus'])->name('check-status');
Route::post('/cek-status', [RegistrationController::class, 'processCheckStatus'])->name('check-status.process');
Route::post('/lupa-akses', [RegistrationController::class, 'recoverAccess'])->name('recover-access');
Route::get('/dashboard-siswa/{number}/{code}', [RegistrationController::class, 'dashboard'])->name('student.dashboard');
Route::get('/pengumuman/{number}/{code}', [RegistrationController::class, 'announcement'])->name('announcement');

// Print Routes
Route::get('/cetak/kartu/{id}', [\App\Http\Controllers\PrintController::class, 'kartu'])->name('print.kartu');
Route::get('/cetak/formulir/{id}', [\App\Http\Controllers\PrintController::class, 'formulir'])->name('print.formulir');

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/master', [App\Http\Controllers\Admin\AdminMasterController::class, 'index'])->name('admin.master.index');
    Route::get('/reports', [App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/export/pdf', [App\Http\Controllers\Admin\AdminReportController::class, 'exportPdf'])->name('admin.reports.exportPdf');
    Route::get('/reports/export/excel', [App\Http\Controllers\Admin\AdminReportController::class, 'exportExcel'])->name('admin.reports.exportExcel');

    Route::get('/registrations/export/pdf', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'exportPdf'])->name('admin.registrations.exportPdf');
    Route::get('/registrations/export/excel', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'exportExcel'])->name('admin.registrations.exportExcel');
    
    Route::get('/registrations', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'index'])->name('admin.registrations.index');
    Route::get('/registrations/{id}', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'show'])->name('admin.registrations.show');
    Route::get('/registrations/{id}/edit', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'edit'])->name('admin.registrations.edit');
    Route::put('/registrations/{id}', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'update'])->name('admin.registrations.update');
    Route::delete('/registrations/{id}', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'destroy'])->name('admin.registrations.destroy');
    Route::post('/registrations/{id}/status', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'updateStatus'])->name('admin.registrations.updateStatus');
    Route::post('/registrations/run-ranking', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'runRanking'])->name('admin.registrations.runRanking');

    Route::get('/settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'update'])->name('admin.settings.update');
    Route::delete('/settings/logo', [App\Http\Controllers\Admin\AdminSettingController::class, 'deleteLogo'])->name('admin.settings.deleteLogo');

    Route::get('/admission-settings', [App\Http\Controllers\Admin\AdmissionSettingController::class, 'index'])->name('admin.admission-settings.index');
    Route::post('/admission-settings', [App\Http\Controllers\Admin\AdmissionSettingController::class, 'update'])->name('admin.admission-settings.update');

    Route::get('/logs', [App\Http\Controllers\Admin\AdminLogController::class, 'index'])->name('admin.logs.index');
    Route::delete('/logs/clear', [App\Http\Controllers\Admin\AdminLogController::class, 'clear'])->name('admin.logs.clear');

    // Admin Users Management
    Route::resource('users', App\Http\Controllers\Admin\AdminUserController::class)->except(['create', 'show', 'edit'])->names([
        'index' => 'admin.users.index',
        'store' => 'admin.users.store',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    // Peminatan feature
    Route::get('/peminatan', [\App\Http\Controllers\AdminInterestController::class, 'index'])->name('admin.interests');
    Route::get('/peminatan/export/pdf', [\App\Http\Controllers\AdminInterestController::class, 'exportPdf'])->name('admin.interests.exportPdf');
    Route::get('/peminatan/export/excel', [\App\Http\Controllers\AdminInterestController::class, 'exportExcel'])->name('admin.interests.exportExcel');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
