<?php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\UserFlowController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\ReportsController;
Route::get('/', function () {
    config(['session.cookie' => 'web_session']);
    return view('welcome');
})->name('welcome');


Route::prefix('admin')->group(function () {
     Route::get('/dashboard', [ComplaintController::class, 'index'])->name('admin.dashboard');
    Route::get('/colleges', [CollegeController::class, 'index'])->name('admin.colleges');
    Route::get('/colleges/complaints', [CollegeController::class, 'complaints'])->name('admin.colleges.complaints');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/users/complaints', [UserController::class, 'complaints'])->name('admin.users.complaints');
    Route::get('/reports', [ReportsController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export', [ReportsController::class, 'export'])->name('admin.reports.export');
    Route::get('/reports/csv', [ReportsController::class, 'exportCsv'])->name('admin.reports.csv');
});
// User Auth Routes (web guard)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [LoginController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth:web');

// User Routes (web guard)
Route::middleware(['auth:web', 'non-admin'])->group(function () {
    Route::get('/home', [ComplaintController::class, 'index'])->name('home');
    Route::get('/complaints/submit', [ComplaintController::class, 'create'])->name('complaints.submit');
    Route::post('/complaints/submit', [ComplaintController::class, 'store']);
    Route::post('complaints/follow-up', [ComplaintController::class, 'followUp'])->name('complaints.follow-up');
});

Route::middleware('auth:web')->group(function () {
    Route::get('/select-college', [UserFlowController::class, 'selectCollege'])->name('select-college');
    Route::post('/select-college', [UserFlowController::class, 'storeCollege']);
    Route::get('/select-role', [UserFlowController::class, 'selectRole'])->name('select-role');
    Route::post('/select-role', [UserFlowController::class, 'storeRole']);
});

// Admin Auth Routes (admin guard)
Route::prefix('admin')->middleware('guest:admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
});
Route::prefix('admin')->post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin Routes (admin guard)
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::patch('/complaint/update', [AdminController::class, 'updateComplaint'])->name('admin.complaint.update');
    Route::get('/complaint/{id}/pdf', [AdminController::class, 'downloadPdf'])->name('admin.complaint.pdf');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export', [AdminController::class, 'reportsExport'])->name('admin.reports.export');
    Route::get('/search', [AdminController::class, 'search'])->name('admin.search');
});