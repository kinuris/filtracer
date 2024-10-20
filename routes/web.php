<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PostController;
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

        Route::middleware('compset')->group(function () {
            Route::get('/alumni', 'dashboardView');
            Route::get('/alumni/profile', 'alumniProfileView');

            Route::get('/alumni/profile/update', 'updateProfileView');
            Route::post('/alumni/profile/update/personal/{alumni}', 'updatePersonalProfile');
            Route::post('/alumni/profile/upload/{alumni}', 'uploadProfilePicture');

            Route::post('/alumni/profile/add/educational/{alumni}', 'addEducationRecord');
            Route::post('/alumni/profile/update/educational/{educ}/{alumni}', 'updateEducationRecord');

            Route::post('/profbio/create/{alumni}', 'createProfBio');
            Route::post('/profbio/update/{alumni}', 'updateProfBio');

            Route::get('/alumni/chat', 'chatView');

            Route::get('/alumni/post', 'postView');
        });
    });

Route::controller(PostController::class)
    ->middleware('auth')
    ->group(function () {
        Route::post('/post/create', 'store');
        Route::post('/post/edit/{post}', 'update');

        Route::get('/post/pin/toggle/{post}', 'togglePinPost');
        Route::get('/post/save/toggle/{post}', 'toggleSavePost');

        Route::get('/post/delete/{post}', 'destroy');
    });

Route::controller(AlertController::class)
    ->middleware('auth')
    ->group(function () {
        Route::get('/alert/complete/{alert}', 'completeAlert');
        Route::get('/alert/gen', 'genAlerts');
        Route::get('/alert/seenall', 'seenAll');
    });

Route::controller(AdminController::class)
    ->middleware('role:Admin')
    ->group(function () {
        Route::get('/admin', 'dashboardView');
        Route::get('/department', 'departmentView');

        Route::get('/profile/report/{alumni}', 'profileReportView');

        Route::get('/department/{department}', 'alumniListView');
        Route::get('/user/view/{user}', 'userView');
        Route::get('/user/delete/{user}', 'userDelete');

        Route::get('/report/graphical', 'reportsGraphicalView');

        Route::get('/report/statistical', 'reportsStatisticalView');
        Route::get('/report/statistical/generate', 'reportsStatisticalGenerateView');

        Route::get('/account', 'accountsView');
        Route::get('/admin/profile', 'myProfileView');
        Route::get('/admin/useraccount/verify/{user}', 'verifyUser');
        Route::get('/admin/useraccount/unverify/{user}', 'unverifyUser');

        Route::get('/audit', 'auditView');

        Route::get('/admin/settings', 'settingsView');

        Route::get('/admin/chat', 'chatView');

        Route::get('/admin/post', 'postView');

        Route::get('/settings/department', 'departmentSettingsView');
        Route::get('/settings/department/edit/{department}', 'editDepartmentView');
        Route::post('/settings/department/update/{department}', 'updateDepartment');

        Route::post('/settings/department/create', 'createDepartment');

        Route::get('/settings/account', 'accountsSettingsView');
        Route::post('/settings/account/edit/{user}', 'accountEdit');
        Route::post('/settings/account/profilepic/{admin}', 'uploadProfilePicture');

        Route::get('/settings/course', 'coursesSettingsView');
        Route::get('/settings/course/edit/{course}', 'editCourseView');
        Route::post('/settings/course/edit/{course}', 'editCourse');
        Route::post('/settings/course/create', 'createCourse');

        Route::get('/settings/major', 'majorsSettingsView');
        Route::get('/settings/major/edit/{major}', 'editMajorView');
        Route::post('/settings/major/edit/{major}', 'editMajor');
        Route::post('/settings/major/create', 'createMajor');

        Route::get('/settings/major/delete/{major}', 'deleteMajorView');
        Route::get('/settings/course/delete/{course}', 'deleteCourseView');
        Route::get('/settings/department/delete/{department}', 'deleteDepartmentView');
    });

Route::controller(ChatController::class)
    ->group(function () {
        Route::get('/chat/messages/{group}', 'fetchChatMessages');
        Route::get('/chat/getgroup/{roomId}', 'getGroup');

        Route::post('/chat/leave/{roomId}', 'leaveGroup');
        Route::post('/chat/rename/{roomId}', 'renameGroup');

        Route::post('/chat/add/{roomId}', 'addMembers');

        Route::post('/chat/makegroup', 'makeGroup');
        Route::post('/chat/send', 'send');
    });
