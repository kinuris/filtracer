<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SuperAdminController;
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

Route::get('/fullurl/{path}', function ($path) {
    $url = urldecode(base64_decode($path));

    $queryParams = http_build_query(request()->input());

    if (str_contains($url, '?')) {
        $url = $url . '&' . $queryParams;
    } else {
        $url = $url . '?' . $queryParams;
    }

    return redirect($url);
});

Route::get('/department/courses/{dept}', function (Department $dept) {
    return response()->json(['courses' => $dept->getCourses()]);
});

Route::controller(AuthController::class)
    ->group(function () {
        // Guest routes
        Route::middleware('guest')->group(function () {
            Route::get('/login', 'loginView')->name('login');
            Route::post('/login', 'login');

            Route::get('/register/admin', 'registerAdminView');
            Route::post('/register/admin', 'registerAdmin');

            Route::get('/register/alumni', 'registerAlumniView');
            Route::post('/register/alumni', 'registerAlumni');

            Route::post('/forgot-password', 'forgetPassword');
        });

        // Auth routes
        Route::middleware('auth')->group(function () {
            Route::post('/switch-account/{user}', 'switchAccount');

            Route::get('/logout', 'logout');
        });
    });


Route::controller(MessageController::class)
    ->group(function () {
        Route::get('/message/send', 'sendMessage');
    });

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

        Route::get('/settings', 'settingsView');
        Route::get('/settings/alumni/password', 'settingsPasswordView');
        Route::post('/settings/alumni/password/{alumni}', 'settingsPassword');

        Route::middleware('compset')->group(function () {
            Route::get('/alumni', 'dashboardView');
            Route::get('/alumni/profile', 'alumniProfileView');
            Route::get('/binding/deny/{binding}', 'denyBinding');
            Route::get('/binding/accept/{binding}', 'acceptBinding');

            Route::get('/alumni/profile/update', 'updateProfileView');
            Route::post('/alumni/profile/update/personal/{alumni}', 'updatePersonalProfile');
            Route::post('/alumni/profile/upload/{alumni}', 'uploadProfilePicture');

            Route::post('/alumni/profile/add/educational/{alumni}', 'addEducationRecord');
            Route::post('/alumni/profile/update/educational/{educ}/{alumni}', 'updateEducationRecord');

            Route::post('/alumni/profile/update/primsec/{primsec}/{alumni}', 'updatePrimarySecondary');

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
        Route::get('/alert/messages', 'genMessages');
        Route::get('/alert/seenall', 'seenAll');
    });


Route::controller(AdminController::class)
    ->middleware('role:Superadmin')
    ->group(function () {
        Route::get('/department', 'departmentView');
        Route::get('/department/{department}', 'alumniListView');

        Route::get('/settings/department', 'departmentSettingsView');
        Route::get('/settings/department/edit/{department}', 'editDepartmentView');
        Route::post('/settings/department/update/{department}', 'updateDepartment');

        Route::post('/settings/department/create', 'createDepartment');
    });

Route::controller(AdminController::class)
    ->middleware('role:Admin')
    ->group(function () {
        Route::get('/admin', 'dashboardView');
        Route::get('/link-account', 'linkAccount')->name('link.index');
        Route::post('/link-create', 'createLink')->name('link.create');
        Route::delete('/link-delete/{alumni}', 'deleteLink')->name('link.delete');

        Route::get('/profile/report/{alumni}', 'profileReportView');

        Route::get('/profiles', 'alumniProfilesView')->name('profiles.index');
        Route::get('/profiles/{course}', 'alumniCoursesView')->name('profiles.courses');

        Route::get('/user/view/{user}', 'userView');
        Route::get('/user/delete/{user}', 'userDelete');
        Route::get('/user/delete/{user}/department', 'userDeleteDepartment');

        Route::get('/report/graphical', 'reportsGraphicalView');

        Route::get('/report/statistical', 'reportsStatisticalView');
        Route::get('/report/statistical/generate', 'reportsStatisticalGenerateView');

        Route::get('/account', 'accountsView');
        Route::get('/admin/profile', 'myProfileView');
        Route::get('/admin/useraccount/verify/{user}', 'verifyUser');
        Route::get('/admin/useraccount/unverify/{user}', 'unverifyUser');

        Route::get('/admin/post/approve/{post}', 'approvePost');
        Route::get('/admin/post/reject/{post}', 'rejectPost');

        Route::get('/audit', 'auditView');

        Route::get('/admin/settings', 'settingsView');

        Route::get('/admin/chat', 'chatView');

        Route::get('/admin/post', 'postView');

        Route::get('/settings/account', 'accountsSettingsView');
        Route::post('/settings/account/edit/{user}', 'accountEdit');
        Route::post('/settings/account/profilepic/{user}', 'uploadProfilePicture');

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

        Route::post('/admin/useraccount/update/{user}', 'updateAccountFromVerify');
        Route::post('/admin/sms/{user}', 'sendSMSIndividual')->name('sendsms.individual');
    });

Route::controller(ChatController::class)
    ->group(function () {
        Route::get('/chat/headers', 'fetchHeaders');
        Route::get('/chat/messages/{group}', 'fetchChatMessages');

        Route::get('/chat/getgroup/{roomId}', 'getGroup');
        Route::get('/chat/group/remove/{groupId}/{user}', 'removeUserFromGroup');

        Route::post('/chat/leave/{roomId}', 'leaveGroup');
        Route::post('/chat/rename/{roomId}', 'renameGroup');

        Route::post('/chat/add/{roomId}', 'addMembers');
        Route::post('/chat/accept/{assoc}', 'acceptAssociation')->name('chat.accept');

        Route::post('/chat/makegroup', 'makeGroup');
        Route::post('/chat/send', 'send');
    });

Route::controller(SuperAdminController::class)
    ->middleware('role:Superadmin')
    ->group(function () {
        Route::get('/account/create-individual', 'createAccountView');
        Route::post('/account/create-individual', 'createAccount');

        Route::post('/account/create-admin', 'createAdmin');

        Route::get('/account/create-bulk', 'createBulkView');
        Route::post('/account/import', 'accountImport');

        Route::post('/account/send-sms-credentials', 'sendSmsCredentials');

        Route::get('/account/imports', 'viewImports');
        Route::post('/post/delete/{post}', 'deletePost');

        Route::post('/manage/account/update/{user}', 'updateAccount');
        Route::get('/post/request', 'postRequestView');

        Route::get('/post/changestat/{post}', 'postChangeStat');
    });

Route::controller(BackupController::class)
    ->middleware('role:Superadmin')
    ->group(function () {
        Route::get('/backup', 'index')->name('backup.index');

        Route::post('/backup/download/{backup}', 'downloadBackup');
        Route::post('/backup/start', 'startBackup');

        Route::post('/backup/upload', 'uploadBackup');

        Route::delete('/backup/delete/{backup}', 'deleteBackup');
        Route::post('/backup/restore/{backup}', 'fromBackup');
    });
