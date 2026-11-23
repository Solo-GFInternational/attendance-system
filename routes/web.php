<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SiteController;
use App\Http\Middleware\RestoreKioskSite;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\AITestController;
use App\Http\Controllers\AttendanceAIController;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('attendance.home');
})->name('home');

/*
|--------------------------------------------------------------------------
| PUBLIC ATTENDANCE
|--------------------------------------------------------------------------
|
| Employees can use these without logging into the system.
|
*/

Route::get('/attendance', function () {
    return view('attendance.home');
})->name('attendance.index');

// Tablet pages: limited to the tablet's own site (see RequireKioskSite).
Route::middleware('kiosk.site')->group(function () {

Route::post('/attendance/register-face/{employee}', [
    AttendanceController::class,
    'registerFaceFirstTime'
])->name('attendance.face.register');

Route::get('/attendance/employee', [
    AttendanceController::class,
    'index'
])->name('attendance.employee');

Route::post('/attendance/sign-in', [
    AttendanceController::class,
    'signIn'
])->name('attendance.sign-in');


Route::post('/attendance/sign-out', [
    AttendanceController::class,
    'signOut'
])->name('attendance.sign-out');


Route::post('/attendance/going-out', [
    AttendanceController::class,
    'goingOut'
])->name('attendance.going-out');


Route::post('/attendance/returning', [
    AttendanceController::class,
    'returning'
])->name('attendance.returning');


Route::get('/attendance/status/{employee}', [
    AttendanceController::class,
    'status'
])->name('attendance.status');


/*
|--------------------------------------------------------------------------
| FACE RECOGNITION
|--------------------------------------------------------------------------
|
| This only identifies the employee.
| It does NOT sign the employee in.
|
*/


/*
|--------------------------------------------------------------------------
| FIRST-TIME EMPLOYEE FACE REGISTRATION
|--------------------------------------------------------------------------
|
| Employee can register a face only if no face has previously
| been registered for that employee.
|
*/


/*
|--------------------------------------------------------------------------
| EMERGENCY ROLL CALL
|--------------------------------------------------------------------------
*/

Route::get('/attendance/emergency', [
    AttendanceController::class,
    'emergency'
])->name('attendance.emergency');

Route::get('/attendance/emergency/pdf', [
    AttendanceController::class,
    'emergencyPdf'
])->name('attendance.emergency.pdf');

Route::post('/attendance/recognize', [
    AttendanceController::class,
    'recognizeFace'
])->name('attendance.recognize');

}); // kiosk.site


/*
|--------------------------------------------------------------------------
| TABLET SITE LINK
|--------------------------------------------------------------------------
|
| /attendance/{site code}, e.g. /attendance/DG. Works for every active site
| using the code entered on the Sites page (upper or lower case).
|
| Keep this AFTER the other /attendance/... pages so it never hides them.
| Their names are blocked as site codes in Site::RESERVED_CODES.
|
*/

Route::get('/attendance/{code}', function (Request $request, string $code) {

    $site = Site::where('active', true)
        ->whereRaw('LOWER(code) = ?', [strtolower($code)])
        ->firstOrFail();

    $current = $request->session()->get('attendance_site_id');
    $changing = $current && (int) $current !== $site->id;

    // Once a tablet is set, only someone who is logged in can move it.
    if ($changing && ! $request->user()) {
        return redirect()
            ->route('attendance.index')
            ->with('error', 'This tablet belongs to another site. Log in to change its site.');
    }

    if ($changing) {
        $request->session()->forget('face_verified');
    }

    RestoreKioskSite::remember($request, $site);

    $response = response()->view('attendance.home');

    // Remove the old site's offline roll call from this device.
    return $changing ? $response->header('Clear-Site-Data', '"storage"') : $response;

})->where('code', '[A-Za-z0-9-]+')->name('attendance.site');


/*
|--------------------------------------------------------------------------
| PUBLIC VISITOR REGISTER
|--------------------------------------------------------------------------
*/

Route::middleware('kiosk.site')->group(function () {

Route::get('/visitors', [
    VisitorController::class,
    'index'
])->name('visitors.index');


Route::post('/visitors/sign-in', [
    VisitorController::class,
    'signIn'
])->name('visitors.sign-in');


Route::patch('/visitors/{visitor}/sign-out', [
    VisitorController::class,
    'signOut'
])->name('visitors.sign-out');

}); // kiosk.site


/*
|--------------------------------------------------------------------------
| ADMIN / MANAGEMENT
|--------------------------------------------------------------------------
|
| These pages require login.
|
*/

Route::middleware('auth')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | ADMIN USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    

Route::resource('admin-users', AdminUserController::class)
    ->parameters(['admin-users' => 'user'])
    ->except(['show'])
    ->middleware('admin');

Route::post('/admin-users/{user}/reset-password', [
    AdminUserController::class,
    'resetPassword'
])
    ->middleware('admin')
    ->name('admin-users.reset-password');



    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        AttendanceController::class,
        'adminDashboard'
    ])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | ADMIN ATTENDANCE DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/attendance', [
        AttendanceController::class,
        'adminDashboard'
    ])->name('attendance.admin');


    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE HISTORY
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/attendance/history', [
        AttendanceController::class,
        'attendanceHistory'
    ])->name('attendance.history');


    Route::get('/admin/attendance/history/pdf', [
        AttendanceController::class,
        'downloadAttendancePdf'
    ])->name('attendance.history.pdf');


    Route::get('/admin/attendance/history/excel', [
        AttendanceController::class,
        'downloadAttendanceExcel'
    ])->name('attendance.history.excel');


    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE OUTING HISTORY
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/attendance/outings', [
        AttendanceController::class,
        'outingHistory'
    ])->name('attendance.outings');

        /*
    |--------------------------------------------------------------------------
    | SITE MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::post('/sites/clear-device', function (Request $request) {
        RestoreKioskSite::forget($request);

        return redirect()
            ->route('sites.index')
            ->with('success', 'This device is no longer set to a site.')
            ->header('Clear-Site-Data', '"storage"');
    })->name('sites.clear-device');

    Route::resource('sites', SiteController::class)
        ->except(['show']);

    Route::patch('/sites/{site}/toggle-status', [
        SiteController::class,
        'toggleStatus'
    ])->name('sites.toggle-status');

    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE IMPORT
    |--------------------------------------------------------------------------
    */

    Route::get('/employees/import', [
        EmployeeController::class,
        'showImport'
    ])->name('employees.import');


    Route::post('/employees/import', [
        EmployeeController::class,
        'import'
    ])->name('employees.import.store');


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::resource('employees', EmployeeController::class);


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE STATUS
    |--------------------------------------------------------------------------
    */

    Route::patch('/employees/{employee}/toggle-status', [
        EmployeeController::class,
        'toggleStatus'
    ])->name('employees.toggle-status');


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE FACE REGISTRATION
    |--------------------------------------------------------------------------
    |
    | Admin registers or updates an employee's face.
    |
    | This does NOT create an attendance record.
    |
    */

    Route::post('/employees/{employee}/face', [
        EmployeeController::class,
        'registerFace'
    ])->name('employees.face.register');


    /*
    |--------------------------------------------------------------------------
    | REMOVE EMPLOYEE FACE
    |--------------------------------------------------------------------------
    */

    Route::delete('/employees/{employee}/face', [
        EmployeeController::class,
        'removeFace'
    ])->name('employees.face.remove');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');


    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');


    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | AI
    |--------------------------------------------------------------------------
    */

Route::get('/ai-test', [AITestController::class, 'test']);
});

Route::post('/admin/attendance-ai/ask', [AttendanceAIController::class, 'ask'])
    ->middleware('auth')
    ->name('attendance.ai.ask');
/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';