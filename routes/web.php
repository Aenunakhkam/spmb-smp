<?php

use App\Http\Controllers\Admin\AdmissionSettingController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminExcellentProgramController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $settings = \App\Models\Setting::whereIn('key', ['academic_year', 'registration_start_date', 'registration_end_date', 'ppdb_agenda', 'frontend_faqs', 'popup_banner', 'social_tiktok', 'social_instagram', 'social_facebook', 'social_x', 'social_youtube', 'contact_whatsapp', 'contact_email'])->pluck('value', 'key');
    $academicYear = $settings['academic_year'] ?? date('Y');
    
    $defaultFaqs = [
        ['question' => 'Bagaimana cara konfirmasi pembayaran?', 'answer' => 'Sistem pendaftaran ini gratis untuk tahap awal. Untuk biaya administrasi lainnya akan diinfokan setelah verifikasi data awal selesai.'],
        ['question' => 'Apakah berkas harus diantar ke sekolah?', 'answer' => 'Tidak perlu. Cukup unggah scan dokumen asli (format JPG/PDF) ke sistem ini. Fisik dokumen dibawa saat tes wawancara.'],
        ['question' => 'Apa yang harus dilakukan setelah mendaftar?', 'answer' => 'Silakan simpan Nomor Pendaftaran dan Kode Akses Anda untuk memantau status verifikasi berkas oleh admin secara berkala.'],
    ];
    $faqs = isset($settings['frontend_faqs']) ? json_decode($settings['frontend_faqs'], true) : $defaultFaqs;
    
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
        'registrationStartDate' => $settings['registration_start_date'] ?? null,
        'registrationEndDate' => $settings['registration_end_date'] ?? null,
        'ppdbAgenda' => json_decode($settings['ppdb_agenda'] ?? '[]', true),
        'academicYear' => $academicYear,
        'faqs' => $faqs,
        'popupBanner' => isset($settings['popup_banner']) ? '/storage/' . $settings['popup_banner'] : null,
        'socialTiktok' => $settings['social_tiktok'] ?? null,
        'socialInstagram' => $settings['social_instagram'] ?? null,
        'socialFacebook' => $settings['social_facebook'] ?? null,
        'socialX' => $settings['social_x'] ?? null,
        'socialYoutube' => $settings['social_youtube'] ?? null,
        'contactWhatsapp' => $settings['contact_whatsapp'] ?? null,
        'contactEmail' => $settings['contact_email'] ?? null,
        'excellentPrograms' => \App\Models\ExcellentProgram::orderBy('id')->get(),
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
    Route::delete('/dokumen/rapor/{id}', [RegistrationController::class, 'deleteGradeProof'])->name('register.deleteGradeProof');
    Route::post('/finalisasi', [RegistrationController::class, 'finalize'])->name('register.finalize');
});

Route::get('/cek-status', [RegistrationController::class, 'checkStatus'])->name('check-status');
Route::post('/cek-status', [RegistrationController::class, 'processCheckStatus'])->name('check-status.process');
Route::post('/lupa-akses', [RegistrationController::class, 'recoverAccess'])->name('recover-access');
Route::get('/dashboard-siswa/{number}/{code}', [RegistrationController::class, 'dashboard'])->name('student.dashboard');
Route::get('/pengumuman/{number}/{code}', [RegistrationController::class, 'announcement'])->name('announcement');

// Print Routes (Preview HTML)
Route::get('/cetak/kartu/{id}', [\App\Http\Controllers\PrintController::class, 'kartu'])->name('print.kartu');
Route::get('/cetak/formulir/{id}', [\App\Http\Controllers\PrintController::class, 'formulir'])->name('print.formulir');

// Print Routes (Download PDF langsung)
Route::get('/unduh/kartu/{id}', [\App\Http\Controllers\PrintController::class, 'downloadKartu'])->name('print.kartu.pdf');
Route::get('/unduh/formulir/{id}', [\App\Http\Controllers\PrintController::class, 'downloadFormulir'])->name('print.formulir.pdf');

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/master', [App\Http\Controllers\Admin\AdminMasterController::class, 'index'])->name('admin.master.index');
    Route::get('/reports', [App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/export/pdf', [App\Http\Controllers\Admin\AdminReportController::class, 'exportPdf'])->name('admin.reports.exportPdf');
    Route::get('/reports/export/excel', [App\Http\Controllers\Admin\AdminReportController::class, 'exportExcel'])->name('admin.reports.exportExcel');
    Route::get('/reports/export/master', [App\Http\Controllers\Admin\AdminReportController::class, 'exportMaster'])->name('admin.reports.exportMaster');

    Route::get('/registrations/export/pdf', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'exportPdf'])->name('admin.registrations.exportPdf');
    Route::get('/registrations/export/excel', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'exportExcel'])->name('admin.registrations.exportExcel');
    
    Route::get('/registrations', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'index'])->name('admin.registrations.index');
    Route::get('/registrations/{id}', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'show'])->name('admin.registrations.show');
    Route::get('/registrations/{id}/edit', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'edit'])->name('admin.registrations.edit');
    Route::put('/registrations/{id}', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'update'])->name('admin.registrations.update');
    Route::delete('/registrations/{id}', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'destroy'])->name('admin.registrations.destroy');
    Route::delete('/registrations/document/{id}', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'deleteDocument'])->name('admin.registrations.deleteDocument');
    Route::post('/registrations/{id}/status', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'updateStatus'])->name('admin.registrations.updateStatus');
    Route::post('/registrations/run-ranking', [App\Http\Controllers\Admin\AdminRegistrationController::class, 'runRanking'])->name('admin.registrations.runRanking');

    Route::get('/settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\AdminSettingController::class, 'update'])->name('admin.settings.update');
    Route::delete('/settings/logo', [App\Http\Controllers\Admin\AdminSettingController::class, 'deleteLogo'])->name('admin.settings.deleteLogo');

    Route::get('/admission-settings', [App\Http\Controllers\Admin\AdmissionSettingController::class, 'index'])->name('admin.admission-settings.index');
    Route::post('/admission-settings', [App\Http\Controllers\Admin\AdmissionSettingController::class, 'update'])->name('admin.admission-settings.update');
    Route::delete('/admission-settings/banner', [App\Http\Controllers\Admin\AdmissionSettingController::class, 'deleteBanner'])->name('admin.admission-settings.deleteBanner');

    Route::get('/logs', [App\Http\Controllers\Admin\AdminLogController::class, 'index'])->name('admin.logs.index');
    Route::delete('/logs/clear', [App\Http\Controllers\Admin\AdminLogController::class, 'clear'])->name('admin.logs.clear');

    // Admin Excellent Programs
    Route::resource('excellent-programs', AdminExcellentProgramController::class)->except(['create', 'show', 'edit'])->names([
        'index' => 'admin.excellent-programs.index',
        'store' => 'admin.excellent-programs.store',
        'update' => 'admin.excellent-programs.update',
        'destroy' => 'admin.excellent-programs.destroy',
    ]);

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
