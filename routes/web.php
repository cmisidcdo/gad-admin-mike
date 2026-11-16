<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\RecycleBin as ControllersRecycleBin;
use App\Http\Controllers\Auth\SignupController;
use App\Livewire\ChangePassword;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; //This is for the `Auth::routes();`
use App\Livewire\DocumentArchiveTable;
use App\Http\Controllers\RecycleBinController;
use App\Livewire\RecycleBin;

use function Termwind\render;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

// Auth routes (registration disabled — we use our own signup controller)
Auth::routes(['register' => false]);

// Signup (self-registration with captcha — accounts pending admin activation)
Route::get('/signup', [SignupController::class, 'showForm'])->name('signup')->middleware('guest');
Route::post('/signup', [SignupController::class, 'store'])->name('signup.store')->middleware('guest');
Route::get('/captcha/refresh', [SignupController::class, 'refreshCaptcha'])->name('captcha.refresh');


Route::group(['middleware' => 'auth'], function () {
    // Your authenticated routes go here
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'index'])->name('home'); // Supposed to be a dashboard. Now, it's home.
    Route::get('/archive', [App\Http\Controllers\ArchiveController::class, 'index'])->name('archive');
    Route::get('/reference', [App\Http\Controllers\ReferenceController::class, 'index'])->name('reference');
    Route::get('/user-administration', [App\Http\Controllers\UserAdministrationController::class, 'index'])->name('user-administration');
    Route::get('/not-yet-submitted', [App\Http\Controllers\NotYetSubmittedController::class, 'index'])->name('not-yet-submitted');
    Route::get('/document-approval', [App\Http\Controllers\DocumentApprovalController::class, 'index'])->name('document-approval');

    // PDF Preview
    Route::get('/preview-pdf/{trans_id}', [DocumentArchiveTable::class, 'previewPdf'])->name('preview-pdf');

    // Download
    Route::get('/download/{trans_id}', [DocumentArchiveTable::class, 'download'])->name('download');

    // Change Password
    Route::get('/change-password', [ChangePasswordController::class, 'index'])->name('change-password');

    // Recycle Bin
    Route::get('/recycle-bin', [RecycleBinController::class, 'index'])->name('change-password');

    // GAD Plan and Budget
    Route::get('/plan-budget', fn () => view('plan-budget.index'))->name('plan-budget');
    Route::get('/plan-budget/{plan}', fn (\App\Models\GadPlan $plan) => view('plan-budget.show', compact('plan')))->name('plan-budget.show');

    // GAD Accomplishment Report
    Route::get('/accomplishment', fn () => view('accomplishment.index'))->name('accomplishment');
    Route::get('/accomplishment/{report}', fn (\App\Models\GadAccomplishment $report) => view('accomplishment.show', compact('report')))->name('accomplishment.show');
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get('/gad-admin-beta/livewire/livewire.js', $handle);
});
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/gad-admin-beta/livewire/update', $handle);
});
