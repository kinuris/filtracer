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

Route::post('/register/admin', [AuthController::class, 'registerAdmin'])
    ->middleware('guest');

Route::get('/register/alumni', [AuthController::class, 'registerAlumniView'])
    ->middleware('guest');

Route::post('/register/alumni', [AuthController::class, 'registerAlumni'])
    ->middleware('guest');

Route::controller(AlumniController::class)
    ->middleware('role:Alumni')
    ->group(function () {
        Route::get('/alumni/setup', 'setupView');
        Route::get('/alumni/setup/personal', 'setupPersonalView');
        Route::post('/alumni/setup/personal/{alumni}', 'setupPersonal');

        Route::get('/alumni/setup/educational', 'setupEducationalView');
        Route::post('/alumni/setup/educational/{alumni}', 'setupEducational');

        Route::get('/alumni/setup/professional', 'setupProfessionalView');
        Route::post('/alumni/setup/professional/{alumni}', 'setupProfessional');

        Route::get('/alumni/setup/profilepic', 'setupProfilepicView');
        Route::post('/alumni/setup/profilepic/{alumni}', 'setupProfilepic');

        Route::get('/alumni', 'dashboardView');
        Route::get('/alumni/profile', 'alumniProfileView');

        Route::get('/alumni/profile/update', 'updateProfileView');
        Route::post('/alumni/profile/update/personal/{alumni}', 'updatePersonalProfile');
        Route::post('/alumni/profile/upload/{alumni}', 'uploadProfilePicture');

        Route::post('/alumni/profile/add/educational/{alumni}', 'addEducationRecord');
        Route::post('/alumni/profile/update/educational/{educ}/{alumni}', 'updateEducationRecord');

        Route::post('/profbio/create/{alumni}', 'createProfBio');
        Route::post('/profbio/update/{alumni}', 'updateProfBio');
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
        Route::get('/report/statistical/generate', 'reportsStatisticalGenerateView');

        Route::get('/account', 'accountsView');
        Route::get('/admin/profile', 'myProfileView');
        Route::get('/admin/useraccount/verify/{user}', 'verifyUser');

        Route::get('/audit', 'auditView');

        Route::get('/admin/settings', 'settingsView');

        Route::get('/settings/department', 'departmentSettingsView');
        Route::get('/settings/department/edit/{department}', 'editDepartmentView');
        Route::post('/settings/department/update/{department}', 'updateDepartment');

        Route::post('/settings/department/create', 'createDepartment');

        Route::get('/settings/account', 'accountsSettingsView');

        Route::get('/settings/course', 'coursesSettingsView');
        Route::get('/settings/course/edit/{course}', 'editCourseView');
        Route::post('/settings/course/create', 'createCourse');

        Route::get('/settings/major', 'majorsSettingsView');
        Route::get('/settings/major/edit/{major}', 'editMajorView');
    });