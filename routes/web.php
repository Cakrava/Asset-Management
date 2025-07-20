<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeploymentDeviceController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FrontPageController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoredDeviceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LettersController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\CheckLoginStatus;
// Middleware untuk menyimpan route terakhir ke session
use Illuminate\Support\Facades\Session;

use App\Models\Ticket;
use App\Models\Transaction;

/*
|--------------------------------------------------------------------------
| Rute Publik & Autentikasi
|--------------------------------------------------------------------------
*/
Route::get('/', [FrontPageController::class, 'index'])->name('front.index') ;
Route::get('login', [AuthController::class, 'login'])->name('auth.login') ;
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('register', [AuthController::class, 'register'])->name('auth.register');
Route::get('register/set-profile', [AuthController::class, 'openProfilePage'])->name('auth.setProfile');
Route::post('login', [AuthController::class, 'authenticate'])->name('auth.authenticate');
Route::post('register', [AuthController::class, 'saveRegister'])->name('auth.register.submit'); 

/*
|--------------------------------------------------------------------------
| Rute Terproteksi (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware([CheckLoginStatus::class])->group(function () {

    // RUTE UNTUK SEMUA ROLE YANG LOGIN
    Route::get('dashboard', [DashboardController::class, 'index'])->name('panel.dashboard');
    Route::get('profile', [UserController::class, 'index'])->name('panel.profile');
    Route::post('profile/update', [UserController::class, 'update'])->name('panel.profile.update');
    Route::post('profile/change-password', [UserController::class, 'changePassword'])->name('panel.profile.changePassword');
    Route::get('/delete-my-account', [UserController::class, 'deleteMyAccount'])->name('panel.account.deleteMyAccount');
    Route::delete('/delete-my-account', [UserController::class, 'destroyMyAccount'])->name('panel.account.destroyMyAccount');
    Route::get('/letters/{letter}/view-archive-signed', [LettersController::class, 'viewSignedArchive'])->name('panel.letter.view_signed_archive');

    // ====================================================================
    // RUTE KHUSUS ROLE: USER
    // ====================================================================
    Route::middleware('role:user')->group(function () {
        Route::get('ticket', [TicketController::class, 'userTicket'])->name('panel.ticket.user-ticket');
        Route::get('message', [MessageController::class, 'index'])->name('panel.message.user-message');
        Route::post('/message/client-message', [MessageController::class, 'userMessage'])->name('panel.chat.send');
        Route::post('tickets/client', [TicketController::class, 'store'])->name('panel.ticket.store');
        Route::post('/tickets/client-cancel', [TicketController::class, 'cancel'])->name('panel.ticket.cancel');
        Route::get('/chat',     [MessageController::class, 'index'])->name('chat.index');
        Route::post('/panel/user/messages/mark-as-read', [MessageController::class, 'markMessagesFromAdminAsRead'])->name('panel.user.messages.markAsRead');
        Route::get('/histories', [HistoryController::class, 'index'])->name('histories.index');
        Route::post('/histories', [HistoryController::class, 'store'])->name('histories.store');
        Route::get('/histories/show', [HistoryController::class, 'show'])->name('histories.show');
    });

    // ====================================================================
    // RUTE KHUSUS ROLE: ADMIN
    // ====================================================================
    Route::middleware('role:admin')->group(function () {
        // Stored Device
        Route::get('stored-device', [StoredDeviceController::class, 'index'])->name('panel.stored-device');
        Route::post('stored-device/store', [StoredDeviceController::class, 'store'])->name('panel.stored-device.store');
        Route::post('stored-device/update', [StoredDeviceController::class, 'update'])->name('panel.stored-device.update');
        Route::delete('stored-device/destroy/{id}', [StoredDeviceController::class, 'destroy'])->name('panel.stored-device.destroy');
        Route::post('stored-device/bulkDestroy', [StoredDeviceController::class, 'bulkDestroy'])->name('panel.stored-device.bulkDestroy');
        Route::get('stored-device/{id}', [StoredDeviceController::class, 'getStoredDeviceData']);
        
        // Client
        Route::get('client', [ClientController::class, 'index'])->name('panel.client');
        Route::post('client/store', [ClientController::class, 'store'])->name('panel.client.store');
        Route::post('client/update', [ClientController::class, 'update'])->name('panel.client.update');
        Route::delete('client/destroy/{id}', [ClientController::class, 'destroy'])->name('panel.client.destroy');
        Route::post('client/bulkDestroy', [ClientController::class, 'bulkDestroy'])->name('panel.client.bulkDestroy');
        Route::get('client/{id}', [ClientController::class, 'getStoredClientData']);
        Route::post('/client/bulk-destroy-duplicates', [ClientController::class, 'bulkDestroyFromDuplicates'])->name('panel.client.bulkDestroyFromDuplicates');
        
        // Ticket Admin
        Route::get('ticket/admin', [TicketController::class, 'adminTicketIndex'])->name('panel.ticket.admin-ticket');
        Route::post('/tickets/{id}/accept', [TicketController::class, 'accept'])->name('ticket.accept');
        Route::post('/tickets/{id}/reject', [TicketController::class, 'reject'])->name('ticket.reject');
        
        // Chat Admin
        Route::get('/panel/admin/chat/conversation/{userId}', [MessageController::class, 'showConversation'])->name('chat.show');
        Route::post('/panel/admin/chat/mark-as-read', [MessageController::class, 'markMessagesAsRead'])->name('panel.admin.chat.markAsRead');
        Route::get('/delete.chat', [MessageController::class, 'deleteChat'])->name('delete.chat');
        Route::post('/chat/send', [MessageController::class, 'sendMessage'])->name('panel.admin.chat.send');

        // Letter Admin
        Route::post('/letters/generate-sst', [LettersController::class, 'generateSst'])->name('admin.letters.generateSst');
        Route::get('/letter', [LettersController::class, 'index'])->name('admin.letter.view');
        Route::post('/letter', [LettersController::class, 'index'])->name('admin.letter.store');
        Route::delete('/panel/letters/{letter}/delete', [LettersController::class, 'softDelete'])->name('panel.letter.softDelete');
    });

    // ====================================================================
    // RUTE KHUSUS ROLE: MASTER
    // ====================================================================
    Route::middleware('role:master')->group(function () {
        Route::get('ticket/master', [TicketController::class, 'masterTicketIndex'])->name('panel.ticket.master-ticket');
    });
    
    // ====================================================================
    // RUTE KHUSUS ROLE: ADMIN & MASTER
    // ====================================================================
    Route::middleware('role:admin,master')->group(function () {
        // User Management
        Route::get('/account-settings', [UserController::class, 'accountSettings'])->name('panel.account.acountSettings');
        Route::post('/account-settings/update', [UserController::class, 'updateAccount'])->name('panel.account.update');
        Route::get('/manage-account', [UserController::class, 'manageAccount'])->name('panel.account.manage');
        Route::post('/generate-account', [UserController::class, 'generateAccount'])->name('panel.account.generate');
        Route::post('/generate-account-administrator', [UserController::class, 'generateAdministrator'])->name('panel.account.generateAdministrator');
        Route::delete('/manage-account/{user}', [UserController::class, 'deleteUser'])->name('panel.account.deleteUser');

        // Device
        Route::get('device', [DeviceController::class, 'index'])->name('panel.device');
        Route::post('device/store', [DeviceController::class, 'store'])->name('panel.device.store');
        Route::post('device/update', [DeviceController::class, 'update'])->name('panel.device.update');
        Route::post('device/destroy/{id}', [DeviceController::class, 'destroy'])->name('panel.device.destroy');
        Route::get('device/{id}', [DeviceController::class, 'getDeviceData']);
        Route::post('device/bulkDestroy', [DeviceController::class, 'bulkDestroy'])->name('panel.device.bulkDestroy');
        Route::post('/device/bulk-destroy-duplicates', [DeviceController::class, 'bulkDestroyFromDuplicates'])->name('panel.device.bulkDestroyFromDuplicates');

        // Tiket
        Route::get('/admin/users/{user}/tickets', [TicketController::class, 'showUserTickets'])->name('admin.user.tickets.show');

        // Letter
        Route::get('/letters/{letter}/view-archive', [LettersController::class, 'viewArchivedPdf'])->name('panel.letter.view_archive');
        Route::get('/letters/{letter}/download-archive', [LettersController::class, 'downloadArchivedPdf'])->name('panel.letter.download_archive');
        Route::post('/letters/store-with-devices', [LettersController::class, 'storeWithDevices'])->name('panel.letter.storeWithDevices');
        
        // Deployment & Transaction
        Route::get('/deployments', [DeploymentDeviceController::class, 'index'])->name('admin.deployment.view');
        Route::get('/asset-flow', [TransactionController::class, 'index'])->name('admin.asset-flow.view');
        Route::get('/api/get-deployed-devices/{user_id}', [TransactionController::class, 'getPreviousDeployment'])->name('admin.transaction.getDeployDevice');
        Route::post('/transaction/from-letter', [TransactionController::class, 'processTransactionFromLetter'])->name('admin.transaction.processFromLetter');
        Route::post('/transaction/manual/other-source', [TransactionController::class, 'processTransactionManualOtherSource'])->name('admin.transaction.processManualOtherSource');
        Route::post('/transaction/manual/selected-client', [TransactionController::class, 'processTransactionManualSelectedClient'])->name('admin.transaction.processManualSelectedClient');
        Route::post('/transaction/manual/deployed', [TransactionController::class, 'processTransactionManualDeployed'])->name('admin.transaction.processManualDeployed');
        Route::get('/api/other-source/search', [TransactionController::class, 'searchOtherSource'])->name('api.otherSource.search');
        Route::post('/complete-letter-submission', [TransactionController::class, 'processSubmission'])->name('letter.process.completion');

        // Report
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('reports.index');
            Route::post('/generate', [ReportController::class, 'generateReport'])->name('reports.generate');
            Route::get('/download-pdf', [ReportController::class, 'downloadPdf'])->name('reports.downloadPdf');
            Route::post('/print-pdf', [ReportController::class, 'printPdf'])->name('reports.printPdf');
            Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');
            Route::get('/view-printable-pdf', [ReportController::class, 'viewPrintablePdf'])->name('reports.viewPrintable');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Rute Debugging
|--------------------------------------------------------------------------
*/
Route::get('/__debuglog', function () {
    $logPath = storage_path('logs/laravel.log');

    if (!file_exists($logPath)) {
        return response('Log file not found.', 404);
    }

    return response()->file($logPath);
});