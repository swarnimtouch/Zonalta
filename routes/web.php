<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\EmployeeLoginController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/import', [ImportController::class, 'index']);
Route::post('/import-employees', [ImportController::class, 'importEmployees'])->name('import.employees');
Route::post('/import-doctors', [ImportController::class, 'importDoctors'])->name('import.doctors');

Route::get('/', [EmployeeLoginController::class, 'showLogin'])->name('login');
Route::post('/login', [EmployeeLoginController::class, 'login'])->name('login.submit');

Route::get('/dashboard', [EmployeeLoginController::class, 'dashboard'])->name('dashboard');
Route::post('/employee/poster', [EmployeeLoginController::class, 'storePoster'])->name('poster.store');

Route::get('/logout', [EmployeeLoginController::class, 'logout'])->name('logout');
Route::get('/result/{poster}', [EmployeeLoginController::class, 'result'])->name('poster.result');
Route::get('/download/banner/{id}', [EmployeeLoginController::class, 'downloadBanner'])->name('download.banner');
Route::get('/download/video/{id}', [EmployeeLoginController::class, 'downloadVideo'])->name('download.video');
Route::prefix('admin')->group(function () {
    Route::get('/login',  [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout',[AdminController::class, 'logout'])->name('admin.logout');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',       [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/banner',         [AdminController::class, 'banner'])->name('banner.index');
    Route::get('/banner/export',  [AdminController::class, 'banner_export'])->name('banner.export');
    Route::post('banner/destroy/{id}', [AdminController::class, 'banner_destroy'])->name('banner.destroy');

    Route::get('/doctor',         [AdminController::class, 'doctor'])->name('doctors.index');
    Route::get('/doctor/export',  [AdminController::class, 'doctor_export'])->name('doctors.export');
    Route::post('doctor/destroy/{id}', [AdminController::class, 'doctor_destroy'])->name('doctors.destroy');

    Route::get('/employee',         [AdminController::class, 'employee'])->name('employees.index');
    Route::get('/employee/export',  [AdminController::class, 'employee_export'])->name('employees.export');
    Route::post('employee/destroy/{id}', [AdminController::class, 'employee_destroy'])->name('employees.destroy');
});
