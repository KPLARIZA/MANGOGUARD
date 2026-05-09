<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PestMonitoringController;
use App\Http\Controllers\PestAlertController;
use App\Http\Controllers\PestAdviceController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\DataAnalyticsController;
use App\Http\Controllers\PestDataController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PestReportController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrapController;
use App\Http\Controllers\FirebaseAuthController;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    if (session()->has('firebase_user_id')) {
        return redirect('/dashboard');
    }

    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['firebase.auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getDashboardStats'])->name('dashboard.stats');
    Route::get('/dashboard/chart/{period?}', [DashboardController::class, 'getChartData'])->name('dashboard.chart');
    Route::get('/dashboard/recent-reports', [DashboardController::class, 'getRecentReports'])->name('dashboard.recent-reports');
    Route::get('/dashboard/export', [DashboardController::class, 'exportData'])->name('dashboard.export');

    // Pest Monitoring
    Route::get('/pest/monitoring', [PestMonitoringController::class, 'index'])->name('pest.monitoring');
    Route::post('/pest/monitoring', [PestMonitoringController::class, 'store'])->name('pest.monitoring.store');
    Route::get('/pest/monitoring/realtime', [PestMonitoringController::class, 'getRealTimeData'])->name('pest.monitoring.realtime');
    Route::get('/pest/monitoring/trends/{period?}', [PestMonitoringController::class, 'getPestTrends'])->name('pest.monitoring.trends');
    Route::get('/pest/monitoring/{id}', [PestMonitoringController::class, 'show'])->name('pest.monitoring.show');
    Route::put('/pest/monitoring/{id}', [PestMonitoringController::class, 'update'])->name('pest.monitoring.update');
    Route::delete('/pest/monitoring/{id}', [PestMonitoringController::class, 'destroy'])->name('pest.monitoring.destroy');

    // Pest Alert
    Route::get('/pest/alerts', [PestAlertController::class, 'index'])->name('pest.alerts');
    Route::post('/pest/alerts', [PestAlertController::class, 'store'])->name('pest.alerts.store');
    Route::get('/pest/alerts/{id}', [PestAlertController::class, 'show'])->name('pest.alerts.show');
    Route::put('/pest/alerts/{id}', [PestAlertController::class, 'update'])->name('pest.alerts.update');
    Route::delete('/pest/alerts/{id}', [PestAlertController::class, 'destroy'])->name('pest.alerts.destroy');

    // Pest Advice
    Route::get('/pest/advice', [PestAdviceController::class, 'index'])->name('pest.advice');
    Route::post('/pest/advice', [PestAdviceController::class, 'store'])->name('pest.advice.store');
    Route::get('/pest/advice/{id}', [PestAdviceController::class, 'show'])->name('pest.advice.show');
    Route::put('/pest/advice/{id}', [PestAdviceController::class, 'update'])->name('pest.advice.update');
    Route::delete('/pest/advice/{id}', [PestAdviceController::class, 'destroy'])->name('pest.advice.destroy');

    // Gallery
    Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
    Route::post('/gallery', [GalleryController::class, 'store'])->name('gallery.store');
    Route::get('/gallery/{id}', [GalleryController::class, 'show'])->name('gallery.show');
    Route::put('/gallery/{id}', [GalleryController::class, 'update'])->name('gallery.update');
    Route::delete('/gallery/{id}', [GalleryController::class, 'destroy'])->name('gallery.destroy');

    // Data Analytics
    Route::get('/data/analytics', [DataAnalyticsController::class, 'index'])->name('data.analytics');
    Route::get('/data/analytics/{period}', [DataAnalyticsController::class, 'getAnalytics'])->name('data.analytics.period');
    Route::get('/data/analytics/{period}/export/{format}', [DataAnalyticsController::class, 'exportData'])->name('data.analytics.export');
    Route::get('/data/trap-performance', [DataAnalyticsController::class, 'getTrapPerformance'])->name('data.trap.performance');
    Route::get('/data/pest-distribution', [DataAnalyticsController::class, 'getPestDistribution'])->name('data.pest.distribution');

    // Pest Data Routes
    Route::get('/pest-data', [PestDataController::class, 'index'])->name('pest.data.index');
    Route::post('/pest-data', [PestDataController::class, 'store'])->name('pest.data.store');
    Route::get('/pest-data/latest', [PestDataController::class, 'getLatestData'])->name('pest.data.latest');
    Route::get('/pest-data/trap/{trap}', [PestDataController::class, 'getTrapData'])->name('pest.data.trap');
    Route::get('/pest-data/{pestData}', [PestDataController::class, 'show'])->name('pest.data.show');
    Route::delete('/pest-data/{pestData}', [PestDataController::class, 'destroy'])->name('pest.data.destroy');

    // Trap Routes
    Route::get('/traps/dashboard', [TrapController::class, 'dashboard'])->name('traps.dashboard');
    Route::get('/traps', [TrapController::class, 'index'])->name('traps.index');
    Route::post('/traps', [TrapController::class, 'store'])->name('traps.store');
    Route::put('/traps/{trap}', [TrapController::class, 'update'])->name('traps.update');
    Route::post('/traps/{trap}/maintenance', [TrapController::class, 'maintenance'])->name('traps.maintenance');
    Route::delete('/traps/{trap}', [TrapController::class, 'destroy'])->name('traps.destroy');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Pest Reports Routes
    Route::get('/pest-reports/create', [PestReportController::class, 'create'])->name('pest-reports.create');
    Route::post('/pest-reports', [PestReportController::class, 'store'])->name('pest-reports.store');
    Route::get('/pest-reports/{report}', [PestReportController::class, 'show'])->name('pest-reports.show');
    Route::get('/pest-reports/{report}/edit', [PestReportController::class, 'edit'])->name('pest-reports.edit');
    Route::put('/pest-reports/{report}', [PestReportController::class, 'update'])->name('pest-reports.update');
    Route::delete('/pest-reports/{report}', [PestReportController::class, 'destroy'])->name('pest-reports.destroy');

    // Pest Reports Index
    Route::get('/pest-reports', [PestReportController::class, 'index'])->name('pest-reports.index');

    // Farms Index
    Route::get('/farms', [FarmController::class, 'index'])->name('farms.index');
    Route::get('/farms/map/{block}', [FarmController::class, 'showMap'])->name('farms.map');
    Route::get('/farms/{farm}', [FarmController::class, 'show'])->name('farms.show');
    Route::post('/farms/{farm}/images', [FarmController::class, 'uploadImage'])->name('farms.images.upload');

    // Profile Routes
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Firebase Auth verification
    Route::post('/auth/verify', [FirebaseAuthController::class, 'verify'])->name('auth.verify');
});
