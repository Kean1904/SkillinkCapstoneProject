<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PesoStaffController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SkilledWorkerWebController;
use App\Http\Controllers\ResidentialWebController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing / Home page
Route::get('/', function () {
    return view('Dashboard');
});

Route::get('/Dashboard', function () {
    return view('Dashboard');
})->name('home');

// LOGIN & REGISTER
Route::get('/Login', function () {
    return view('Login');
})->name('Login');

Route::post('/Login', [LoginController::class, 'authenticate'])->name('Login.submit');

Route::get('/Register', function () {
    return view('Register');
})->name('Register');

Route::post('/Register', [RegisterController::class, 'store'])->name('Register.submit');

Route::post('/password/forgot', [LoginController::class, 'forgotPassword'])->name('password.forgot');
Route::get('/password/reset/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/password/reset', [LoginController::class, 'updatePasswordWithToken'])->name('password.reset.submit');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 1. SKILLED WORKER ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/SkilledWorker', function () {
    $availableJobs = \App\Models\JobPost::where('status', '!=', 'Completed')->count();
    $pendingJobs = \App\Models\JobPost::where('status', 'Pending')->count();
    $jobsList = \App\Models\JobPost::where('status', '!=', 'Completed')->latest()->take(10)->get();
    return view('dashboard.SkilledWorker', compact('availableJobs', 'pendingJobs', 'jobsList'));
})->name('dashboard.SkilledWorker');

Route::get('/skilled-worker/tracking-service', [SkilledWorkerWebController::class, 'trackingService'])->name('skilled_worker.tracking_service');
Route::post('/skilled-worker/job/{id}/apply', [SkilledWorkerWebController::class, 'applyJob'])->name('skilled_worker.job.apply');
Route::post('/skilled-worker/bookings/{id}/status', [SkilledWorkerWebController::class, 'updateBookingStatus'])->name('skilled_worker.booking.update_status');
Route::get('/skilled-worker/my-services', [SkilledWorkerWebController::class, 'myServices'])->name('skilled_worker.my_services');
Route::post('/skilled-worker/services/update', [SkilledWorkerWebController::class, 'updateServices'])->name('skilled_worker.services.update');
Route::get('/skilled-worker/profile', [SkilledWorkerWebController::class, 'profile'])->name('skilled_worker.profile');
Route::post('/skilled-worker/profile/update', [SkilledWorkerWebController::class, 'updateProfile'])->name('skilled_worker.profile.update');
Route::get('/skilled-worker/settings', [SkilledWorkerWebController::class, 'settings'])->name('skilled_worker.settings');
Route::post('/skilled-worker/password/update', [SkilledWorkerWebController::class, 'updatePassword'])->name('skilled_worker.password.update');

/*
|--------------------------------------------------------------------------
| 2. RESIDENTIAL CLIENT ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/Residential', function () {
    $user = \App\Models\User::where('name', session('user_name'))->first();
    $availableWorkers = \App\Models\User::where('role', 'skilled worker')->count();
    $postedJobs = $user ? \App\Models\JobPost::where('client_id', $user->user_id)->orWhere('posted_by', $user->name)->count() : 0;
    $workersList = \App\Models\User::where('role', 'skilled worker')->latest()->take(10)->get();
    return view('dashboard.Residential', compact('availableWorkers', 'postedJobs', 'workersList'));
})->name('dashboard.Residential');

Route::get('/residential/hiring-history', [ResidentialWebController::class, 'hiringHistory'])->name('residential.hiring_history');
Route::post('/residential/review/submit', [ResidentialWebController::class, 'submitReview'])->name('residential.review.submit');
Route::post('/residential/complaint/submit', [ResidentialWebController::class, 'submitComplaint'])->name('residential.complaint.submit');
Route::get('/residential/saved-workers', [ResidentialWebController::class, 'savedWorkers'])->name('residential.saved_workers');
Route::post('/residential/worker/{id}/toggle-save', [ResidentialWebController::class, 'toggleSaveWorker'])->name('residential.worker.toggle_save');
Route::post('/residential/booking/create', [ResidentialWebController::class, 'createBooking'])->name('residential.booking.create');
Route::get('/residential/job-posts', [ResidentialWebController::class, 'jobPosts'])->name('residential.job_posts');
Route::post('/residential/job/create', [ResidentialWebController::class, 'createJob'])->name('residential.job.create');
Route::get('/residential/profile', [ResidentialWebController::class, 'profile'])->name('residential.profile');
Route::post('/residential/profile/update', [ResidentialWebController::class, 'updateProfile'])->name('residential.profile.update');
Route::get('/residential/settings', [ResidentialWebController::class, 'settings'])->name('residential.settings');
Route::post('/residential/password/update', [ResidentialWebController::class, 'updatePassword'])->name('residential.password.update');

/*
|--------------------------------------------------------------------------
| 3. PESO STAFF ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/PesoStaff', [PesoStaffController::class, 'index'])->name('dashboard.PesoStaff');
Route::get('/peso-staff/accreditation', [PesoStaffController::class, 'accreditation'])->name('peso_staff.accreditation');
Route::post('/dashboard/PesoStaff/accredit/{id}', [PesoStaffController::class, 'accreditWorker'])->name('peso.accredit');
Route::get('/peso-staff/job-tracking', [PesoStaffController::class, 'jobTracking'])->name('peso_staff.job_tracking');
Route::get('/peso-staff/complaints', [PesoStaffController::class, 'complaints'])->name('peso_staff.complaints');
Route::post('/dashboard/PesoStaff/complaint/{id}/resolve', [PesoStaffController::class, 'resolveComplaint'])->name('peso.resolve_complaint');
Route::get('/peso-staff/reports', [PesoStaffController::class, 'reports'])->name('peso_staff.reports');
Route::get('/peso-staff/announcements', [PesoStaffController::class, 'announcements'])->name('peso_staff.announcements');
Route::post('/peso-staff/announcements/broadcast', [PesoStaffController::class, 'broadcastAnnouncement'])->name('peso_staff.announcements.broadcast');
Route::get('/peso-staff/profile', [PesoStaffController::class, 'profile'])->name('peso_staff.profile');
Route::post('/peso-staff/profile/update', [PesoStaffController::class, 'updateProfile'])->name('peso_staff.profile.update');
Route::get('/peso-staff/settings', [PesoStaffController::class, 'settings'])->name('peso_staff.settings');
Route::post('/peso-staff/password/update', [PesoStaffController::class, 'updatePassword'])->name('peso_staff.password.update');

/*
|--------------------------------------------------------------------------
| 4. MUNICIPAL ADMINISTRATOR ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/Admin', [AdminDashboardController::class, 'index'])->name('dashboard.Admin');
Route::get('/admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
Route::get('/admin/staff', [AdminDashboardController::class, 'staff'])->name('admin.staff');
Route::get('/admin/categories', [AdminDashboardController::class, 'categories'])->name('admin.categories');
Route::get('/admin/audit-logs', [AdminDashboardController::class, 'auditLogs'])->name('admin.audit_logs');
Route::get('/admin/profile', [AdminDashboardController::class, 'profile'])->name('admin.profile');
Route::post('/admin/profile/update', [AdminDashboardController::class, 'updateProfile'])->name('admin.profile.update');
Route::get('/admin/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
Route::post('/admin/password/update', [AdminDashboardController::class, 'updatePassword'])->name('admin.password.update');