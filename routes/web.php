<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SettingController;

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

Route::middleware(['cors'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('view.login');
    });
    
    
    Route::prefix('login')->group(function () {
        Route::get('/', [UserController::class, 'loginView'])->name('view.login');
        
    });
    
    Route::prefix('users')->group(function () { 
        Route::get('/', [UserController::class, 'viewUsers'])->name('view.users');
        Route::get('/profile', [UserController::class, 'viewProfile'])->name('view.profile');
        Route::post('/login', [UserController::class, 'loginAccount']);
        Route::post('/logout', [UserController::class, 'logoutAccount']);
        Route::post('/deactivate-reactivate', [UserController::class, 'changeStatus']); 
        Route::post('/create-client-account', [UserController::class, 'saveClientUser']);
        Route::post('/create-account', [UserController::class, 'saveUser']);
        Route::post('/edit-profile', [UserController::class, 'editProfile']);
        Route::post('/reset-password', [UserController::class, 'resetPassword']);
        Route::post('/system-maintenance', [UserController::class, 'systemMaintenance']);
    });

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'viewClients'])->name('view.clients');
        Route::get('/{reference}', [ClientController::class, 'viewClient'])->name('view.client'); 
        Route::post('/client/create', [ClientController::class, 'saveClient']);
        Route::get('/view/utilities', [ClientController::class, 'viewUtilities'])->name('view.utilities');
        Route::post('/client/mark-as-paid', [ClientController::class, 'markAsPaid']);

        Route::post('/client/notify', [ClientController::class, 'notifyClient']);
        Route::post('/deactivate-reactivate', [ClientController::class, 'changeStatus']);
        Route::post('/incident-report', [ClientController::class, 'generateIncidentReport']);
        Route::post('/incident-report/change-status', [ClientController::class, 'incidentReportChangeStatus']);
    });

    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'viewAnnouncements'])->name('view.announcements');
    });

    Route::prefix('bills')->group(function () {
        Route::get('/', [ClientController::class, 'viewBill'])->name('view.bill');
        Route::post('/generate', [ClientController::class, 'generateBill']);
        
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'viewWaterBill'])->name('view.water.bill');
        Route::post('/create-bill', [SettingController::class, 'saveBill']);
    });

    Route::prefix('reports')->group(function () {
        Route::get('/', [UserController::class, 'viewReports'])->name('view.reports');
    });
    
});


