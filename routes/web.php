<?php

use Illuminate\Support\Facades\Route;
// Tambah route keluarga
require __DIR__.'/families.php';
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarningLetterController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'role:superadmin,top_level_management,internal_hr'])->name('dashboard');
Route::get('/recruitment-dashboard', [DashboardController::class, 'recruitmentDashboard'])->middleware(['auth', 'role:recruitmentteam'])->name('recruitment.dashboard');

// Task Management (top_level_management & recruitmentteam)
Route::middleware(['auth', 'role:superadmin,top_level_management,recruitmentteam,internal_hr'])->group(function () {
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::post('/tasks/{task}/checklists', [TaskController::class, 'addChecklist'])->name('tasks.add-checklist');
    Route::post('/checklists/{checklist}/toggle', [TaskController::class, 'toggleChecklist'])->name('checklists.toggle');
    Route::delete('/checklists/{checklist}', [TaskController::class, 'deleteChecklist'])->name('checklists.delete');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.add-comment');
    Route::delete('/comments/{comment}', [TaskController::class, 'deleteComment'])->name('comments.delete');
    Route::post('/tasks/{task}/attachments', [TaskController::class, 'addAttachment'])->name('tasks.add-attachment');
    Route::delete('/attachments/{attachment}', [TaskController::class, 'deleteAttachment'])->name('attachments.delete');
});

// Authenticated routes for managing question banks
Route::middleware(['auth', 'role:superadmin,recruitmentteam'])->group(function () {
    Route::resource('banks', BankController::class);
    Route::post('/banks/{bank}/credentials', [BankController::class, 'generateApplicantCredential'])->name('banks.credentials.generate');
    // AJAX: fetch credentials list (JSON) for bank (used by client-side sorting)
    Route::get('/banks/{bank}/credentials', [BankController::class, 'credentialsList'])->name('banks.credentials.list');
    Route::delete('/banks/{bank}/credentials/{credential}', [BankController::class, 'deleteApplicantCredential'])->name('banks.credentials.delete');
    Route::get('/banks/{bank}/credentials/template', [BankController::class, 'downloadCredentialTemplate'])->name('banks.credentials.template');
    Route::get('/banks/{bank}/credentials/export', [BankController::class, 'exportCredentials'])->name('banks.credentials.export');
    Route::post('/banks/{bank}/credentials/import', [BankController::class, 'importCredentials'])->name('banks.credentials.import');
    Route::post('/banks/{bank}/credentials/delete-multiple', [BankController::class, 'deleteMultipleCredentials'])->name('banks.credentials.delete_multiple');
    Route::get('/banks/{bank}/results', [BankController::class, 'results'])->name('banks.results');

    Route::post('/banks/{bank}/toggle', [BankController::class, 'toggleBank'])->name('banks.toggle');

    // Sub-test routes
    Route::get('/sub-tests/{subTest}/edit', [BankController::class, 'editSubTest'])->name('sub-tests.edit');
    Route::put('/sub-tests/{subTest}', [BankController::class, 'updateSubTest'])->name('sub-tests.update');
    Route::delete('/sub-tests/{subTest}', [BankController::class, 'deleteSubTest'])->name('sub-tests.delete');

    // Question routes
    Route::get('/questions/{question}/edit', [BankController::class, 'editQuestion'])->name('questions.edit');
    Route::put('/questions/{question}', [BankController::class, 'updateQuestion'])->name('questions.update');
    Route::delete('/questions/{question}', [BankController::class, 'deleteQuestion'])->name('questions.delete');
    Route::get('/banks/{bank}/participant/{response}/pdf', [BankController::class, 'exportParticipantPdf'])->name('banks.export-participant-pdf');
    Route::get('/banks/{bank}/export-excel', [BankController::class, 'exportExcel'])->name('banks.export-excel');
    Route::get('/cheat-log', [BankController::class, 'cheatLog'])->name('banks.cheat-log');
});

// Authenticated routes for managing employees and departments
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('departments', DepartmentController::class);
    Route::resource('users', UserController::class);
});

// Employees access: allow Internal HR and Superadmin to manage employee database
Route::middleware(['auth', 'role:superadmin,internal_hr'])->group(function () {
    Route::resource('employees', EmployeeController::class);
    Route::get('/employees-import', [EmployeeController::class, 'showImport'])->name('employees.import-form');
    Route::post('/employees-import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('/employees-template', [EmployeeController::class, 'downloadTemplate'])->name('employees.template');
    Route::get('/employees-export', [EmployeeController::class, 'exportExcel'])->name('employees.export');
});

// Authenticated routes for managing warning letters (Surat Peringatan)
Route::middleware(['auth', 'role:superadmin,admin_prod,internal_hr'])->group(function () {
    Route::resource('warning-letters', WarningLetterController::class)->except(['show']);
    Route::get('/warning-letters/{warning_letter}/pdf', [WarningLetterController::class, 'showPdf'])->name('warning-letters.show-pdf');
    Route::get('/warning-letters/{warning_letter}/download-pdf', [WarningLetterController::class, 'downloadPdf'])->name('warning-letters.download-pdf');
    Route::get('/warning-letters-export', [WarningLetterController::class, 'exportExcel'])->name('warning-letters.export');
    Route::get('/warning-letters-import', [WarningLetterController::class, 'showImport'])->name('warning-letters.import-form');
    Route::post('/warning-letters-import', [WarningLetterController::class, 'import'])->name('warning-letters.import');
    Route::get('/warning-letters-template', [WarningLetterController::class, 'downloadTemplate'])->name('warning-letters.template');
    Route::get('/warning-letters/{warning_letter}/sign', [WarningLetterController::class, 'showSign'])->name('warning-letters.sign-form');
    Route::post('/warning-letters/{warning_letter}/sign', [WarningLetterController::class, 'sign'])->name('warning-letters.sign');
});

// Public test routes
Route::get('/test/take/{token}', [TestController::class, 'show'])->name('test.show');
Route::post('/test/take/{token}/submit', [TestController::class, 'submit'])->name('test.submit');
Route::get('/test/take/{token}/thankyou', [TestController::class, 'thankyou'])->name('test.thankyou');
Route::get('/test/{slug}', [TestController::class, 'register'])->name('test.register');
Route::post('/test/{slug}/start', [TestController::class, 'start'])->name('test.start');
Route::get('/test/{slug}/biodata', [TestController::class, 'biodata'])->name('test.biodata');
Route::post('/test/{slug}/verify', [TestController::class, 'verify'])->name('test.verify');

// Serve question images (from DB or storage)
Route::get('/questions/{question}/image', [BankController::class, 'getQuestionImage'])->name('questions.image');

// Activity Logs (superadmin only)
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::delete('/activity-logs', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');
});

// Serve storage files in development if storage:link is not available
Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    abort(404);
})->where('path', '.*')->name('storage.serve');


