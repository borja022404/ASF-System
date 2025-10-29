<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Farmer\ReportController;
use App\Http\Controllers\Farmer\CaseNoteController as FarmerCaseNoteController;
use App\Http\Controllers\Vet\ReportController as VetReportController;
use App\Http\Controllers\Vet\CaseNoteController;
use App\Http\Controllers\Admin\CaseNoteController as AdminCaseNoteController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BackupController;





Route::get('/', function () {
    return view('home');
})->name('home');


// The main dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Fix: Change GET to POST to match your JavaScript request
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');

    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/count', [NotificationController::class, 'unreadCount']);
});



// admin routes here 
Route::middleware(['auth', 'can:admin-access'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::patch('/{user}', 'update')->name('update');
        Route::delete('/{user}', 'destroy')->name('destroy');
    });

    Route::controller(AdminReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{report}', 'show')->name('show');
        Route::delete('/{report}', 'destroy')->name('destroy');
        Route::get('/reports/analysis', 'analysis')->name('analysis');
        Route::get('/reports/map', 'reportsMap')->name('map');
        Route::get('/{report}/edit', 'edit')->name('edit');
        Route::patch('health/{report}', 'healthupdate')->name('healthupdate');
        Route::patch('report/{report}', 'reportupdate')->name('reportupdate');
        Route::get('/reports/export', 'export')->name('export');
    });
    Route::post('/reports/{report}/notes', [AdminCaseNoteController::class, 'store'])->name('notes.store');


    Route::get('/admin/backup', [BackupController::class, 'manualBackup'])->name('backup.manual');
    Route::get('/admin/backups', [BackupController::class, 'listBackups'])->name('backup.list');
    Route::get('/admin/backups/download/{filename}', [BackupController::class, 'downloadBackup'])->name('backup.download');

});



Route::prefix('vet')->name('vet.')->middleware('can:vet-access')->group(function () {
    Route::controller(VetReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/analysis', 'analysis')->name('analysis');
        Route::get('/underreview', 'UnderReview')->name('underreview');
        Route::get('/unassessedreview', 'UnassessedReview')->name('unassessed');
        Route::get('/high-risk', 'highRisk')->name('high_risk');
        Route::get('/resolved', 'resolved')->name('resolved');
        Route::get('/{report}', 'show')->name('show');
        Route::patch('health/{report}', 'healthupdate')->name('healthupdate');
        Route::patch('report/{report}', 'reportupdate')->name('reportupdate');
    });
    Route::post('/reports/{report}/notes', [CaseNoteController::class, 'store'])->name('notes.store');


});

Route::prefix('farmer')->name('farmer.')->middleware('can:farmer-access')->group(function () {
    Route::controller(ReportController::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/', 'index')->name('index');
        Route::get('/resolved', 'resolved')->name('resolved');
        Route::get('/submitted', 'submitted')->name('submitted');
        Route::get('/inspection', 'inspection')->name('inspection');
        Route::get('/{report}', 'show')->name('show');
        Route::get('/{report}/edit', 'edit')->name('edit');
        Route::patch('/{report}', 'update')->name('update');
    });

    Route::post('/reports/{report}/notes', [FarmerCaseNoteController::class, 'store'])->name('notes.store');
    // routes/web.php
    Route::post('/farmer/notes/{note}/reply', [FarmerCaseNoteController::class, 'reply'])->name('notes.reply');


});




require __DIR__ . '/auth.php';
