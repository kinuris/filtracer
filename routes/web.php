<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = Auth::user();

    if (Auth::check()) {
        if ($user->role == 'Admin') {
            return redirect('/admin');
        } else {
            return redirect('/alumni');
        }
    }
})->middleware('auth');

Route::get('/department/courses/{dept}', function (Department $dept) {
    return response()->json(['courses' => $dept->getCourses()]);
});

Route::get('/login', [AuthController::class, 'loginView'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('auth');

Route::get('/register/admin', [AuthController::class, 'registerAdminView'])
    ->middleware('guest');

Route::post('/register/admin', [AuthController::class, 'registerAdmin'])
    ->middleware('guest');

Route::get('/register/alumni', [AuthController::class, 'registerAlumniView'])
    ->middleware('guest');

Route::post('/register/alumni', [AuthController::class, 'registerAlumni'])
    ->middleware('guest');

Route::controller(AlumniController::class)
    ->middleware('role:Alumni')
    ->group(function () {
        Route::get('/alumni', 'dashboardView');
    });

Route::controller(AdminController::class)
    ->middleware('role:Admin')
    ->group(function () {
        Route::get('/admin', 'dashboardView');
        Route::get('/department', 'departmentView');

        Route::get('/department/{department}', 'alumniListView');
        Route::get('/user/view/{user}', 'userView');

        Route::get('/report/graphical', 'reportsGraphicalView');
        Route::get('/report/statistical', 'reportsStatisticalView');

        Route::get('/account', 'accountsView');
        Route::get('/admin/profile', 'myProfileView');

        Route::get('/audit', 'auditView');

        Route::get('/admin/settings', 'settingsView');
        Route::get('/settings/department', 'departmentSettingsView');
        Route::get('/settings/account', 'accountsSettingsView');
    });
