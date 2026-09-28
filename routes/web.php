<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PesoStaffController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SkilledWorkerWebController;
use App\Http\Controllers\HouseholdClientController;
use App\Http\Controllers\DashboardStatsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Live Dashboard Stats Polling Endpoint (Shared with mobile & all web dashboards)
Route::get('/dashboard/live-stats', [DashboardStatsController::class, 'getLiveStats'])->name('dashboard.live_stats');

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
Route::post('/user/consent/accept', [LoginController::class, 'acceptConsent'])->name('user.consent.accept');

/*
|--------------------------------------------------------------------------
| 1. SKILLED WORKER ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/SkilledWorker', function () {
    $worker = \App\Models\User::where('name', session('user_name'))->first()
        ?? \App\Models\User::where('role', 'skilled worker')->first()
        ?? new \App\Models\User(['name' => 'juan_plumber', 'first_name' => 'Juan', 'last_name' => 'Dela Cruz']);

    $availableJobs = \App\Models\JobPost::where(function ($q) {
        $q->whereNull('applicant_username')
          ->orWhere('applicant_username', '');
    })->whereNotIn('status', ['Completed', 'Cancelled'])->count();

    $pendingJobs = \App\Models\JobPost::whereNotNull('applicant_username')
        ->where('applicant_username', '!=', '')
        ->whereNotIn('status', ['Completed', 'Cancelled'])
        ->count();

    $jobsList = \App\Models\JobPost::whereNotIn('status', ['Completed', 'Cancelled'])->latest('created_at')->take(10)->get();
    return view('dashboard.SkilledWorker', compact('availableJobs', 'pendingJobs', 'jobsList', 'worker'));
})->name('dashboard.SkilledWorker');

Route::get('/skilled-worker/tracking-service', [SkilledWorkerWebController::class, 'trackingService'])->name('skilled_worker.tracking_service');
Route::post('/skilled-worker/complaint/submit', [SkilledWorkerWebController::class, 'submitComplaint'])->name('skilled_worker.complaint.submit');
Route::post('/skilled-worker/job/{id}/apply', [SkilledWorkerWebController::class, 'applyJob'])->name('skilled_worker.job.apply');
Route::post('/skilled-worker/bookings/{id}/status', [SkilledWorkerWebController::class, 'updateBookingStatus'])->name('skilled_worker.booking.update_status');
Route::get('/skilled-worker/my-services', [SkilledWorkerWebController::class, 'myServices'])->name('skilled_worker.my_services');
Route::post('/skilled-worker/job-offer/create', [SkilledWorkerWebController::class, 'createJobOffer'])->name('skilled_worker.job_offer.create');
Route::post('/skilled-worker/services/update', [SkilledWorkerWebController::class, 'updateServices'])->name('skilled_worker.services.update');
Route::post('/skilled-worker/submit-application', [SkilledWorkerWebController::class, 'submitApplication'])->name('skilled_worker.submit_application');
Route::get('/skilled-worker/profile', [SkilledWorkerWebController::class, 'profile'])->name('skilled_worker.profile');
Route::post('/skilled-worker/profile/update', [SkilledWorkerWebController::class, 'updateProfile'])->name('skilled_worker.profile.update');
Route::get('/skilled-worker/settings', [SkilledWorkerWebController::class, 'settings'])->name('skilled_worker.settings');
Route::post('/skilled-worker/password/update', [SkilledWorkerWebController::class, 'updatePassword'])->name('skilled_worker.password.update');

/*
|--------------------------------------------------------------------------
| 2. HOUSEHOLD CLIENT (FORMERLY RESIDENTIAL) ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/HouseholdClient', [HouseholdClientController::class, 'index'])->name('dashboard.HouseholdClient');
Route::get('/dashboard/Residential', [HouseholdClientController::class, 'index'])->name('dashboard.Residential');

Route::get('/household-client/hiring-history', [HouseholdClientController::class, 'hiringHistory'])->name('household_client.hiring_history');
Route::post('/household-client/review/submit', [HouseholdClientController::class, 'submitReview'])->name('household_client.review.submit');
Route::post('/household-client/complaint/submit', [HouseholdClientController::class, 'submitComplaint'])->name('household_client.complaint.submit');
Route::get('/household-client/saved-workers', [HouseholdClientController::class, 'savedWorkers'])->name('household_client.saved_workers');
Route::post('/household-client/worker/{id}/toggle-save', [HouseholdClientController::class, 'toggleSaveWorker'])->name('household_client.worker.toggle_save');
Route::get('/household-client/worker-booked-dates/{username}', [HouseholdClientController::class, 'getWorkerBookedDates'])->name('household_client.worker_booked_dates');
Route::post('/household-client/booking/create', [HouseholdClientController::class, 'createBooking'])->name('household_client.booking.create');
Route::get('/household-client/job-posts', [HouseholdClientController::class, 'jobPosts'])->name('household_client.job_posts');
Route::post('/household-client/job/create', [HouseholdClientController::class, 'createJob'])->name('household_client.job.create');
Route::post('/household-client/job/{id}/complete', [HouseholdClientController::class, 'completeJob'])->name('household_client.job.complete');
Route::get('/household-client/profile', [HouseholdClientController::class, 'profile'])->name('household_client.profile');
Route::post('/household-client/profile/update', [HouseholdClientController::class, 'updateProfile'])->name('household_client.profile.update');
Route::get('/household-client/settings', [HouseholdClientController::class, 'settings'])->name('household_client.settings');
Route::post('/household-client/password/update', [HouseholdClientController::class, 'updatePassword'])->name('household_client.password.update');

// Backward compatibility aliases
Route::get('/residential/hiring-history', [HouseholdClientController::class, 'hiringHistory'])->name('residential.hiring_history');
Route::post('/residential/review/submit', [HouseholdClientController::class, 'submitReview'])->name('residential.review.submit');
Route::post('/residential/complaint/submit', [HouseholdClientController::class, 'submitComplaint'])->name('residential.complaint.submit');
Route::get('/residential/saved-workers', [HouseholdClientController::class, 'savedWorkers'])->name('residential.saved_workers');
Route::post('/residential/worker/{id}/toggle-save', [HouseholdClientController::class, 'toggleSaveWorker'])->name('residential.worker.toggle_save');
Route::get('/residential/worker-booked-dates/{username}', [HouseholdClientController::class, 'getWorkerBookedDates'])->name('residential.worker_booked_dates');
Route::post('/residential/booking/create', [HouseholdClientController::class, 'createBooking'])->name('residential.booking.create');
Route::get('/residential/job-posts', [HouseholdClientController::class, 'jobPosts'])->name('residential.job_posts');
Route::post('/residential/job/create', [HouseholdClientController::class, 'createJob'])->name('residential.job.create');
Route::post('/residential/job/{id}/complete', [HouseholdClientController::class, 'completeJob'])->name('residential.job.complete');
Route::get('/residential/profile', [HouseholdClientController::class, 'profile'])->name('residential.profile');
Route::post('/residential/profile/update', [HouseholdClientController::class, 'updateProfile'])->name('residential.profile.update');
Route::get('/residential/settings', [HouseholdClientController::class, 'settings'])->name('residential.settings');
Route::post('/residential/password/update', [HouseholdClientController::class, 'updatePassword'])->name('residential.password.update');

/*
|--------------------------------------------------------------------------
| 3. PESO STAFF ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/dashboard/PesoStaff', [PesoStaffController::class, 'index'])->name('dashboard.PesoStaff');
Route::get('/peso-staff/users', [PesoStaffController::class, 'users'])->name('peso_staff.users');
Route::get('/peso-staff/accreditation', [PesoStaffController::class, 'accreditation'])->name('peso_staff.accreditation');
Route::get('/peso-staff/worker/{id}/credentials', [PesoStaffController::class, 'viewCredentials'])->name('peso_staff.worker_credentials');
Route::post('/dashboard/PesoStaff/accredit/{id}', [PesoStaffController::class, 'accreditWorker'])->name('peso.accredit');
Route::post('/dashboard/PesoStaff/unaccredit/{id}', [PesoStaffController::class, 'unaccreditWorker'])->name('peso.unaccredit');
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
Route::post('/admin/audit-logs/reset', [AdminDashboardController::class, 'resetAuditLogs'])->name('admin.audit_logs.reset');
Route::get('/admin/complaints', [AdminDashboardController::class, 'complaints'])->name('admin.complaints');
Route::post('/admin/complaint/{id}/resolve', [AdminDashboardController::class, 'resolveComplaint'])->name('admin.resolve_complaint');
Route::get('/admin/announcements', [AdminDashboardController::class, 'announcements'])->name('admin.announcements');
Route::post('/admin/announcements/broadcast', [AdminDashboardController::class, 'broadcastAnnouncement'])->name('admin.announcements.broadcast');
Route::get('/admin/tesda-reports', [AdminDashboardController::class, 'tesdaReports'])->name('admin.tesda_reports');
Route::get('/admin/tesda-reports/live-data', [AdminDashboardController::class, 'tesdaReportsLiveData'])->name('admin.tesda_reports.live_data');
Route::get('/admin/dole-reports', [AdminDashboardController::class, 'doleReports'])->name('admin.dole_reports');
Route::get('/admin/profile', [AdminDashboardController::class, 'profile'])->name('admin.profile');
Route::post('/admin/profile/update', [AdminDashboardController::class, 'updateProfile'])->name('admin.profile.update');
Route::get('/admin/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
Route::post('/admin/password/update', [AdminDashboardController::class, 'updatePassword'])->name('admin.password.update');