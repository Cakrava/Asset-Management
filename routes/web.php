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

//autentikasi



Route::get('/', [FrontPageController::class, 'index'])->name('front.index') ;
Route::get('login', [AuthController::class, 'login'])->name('auth.login') ;
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('register', [AuthController::class, 'register'])->name('auth.register');
Route::get('register/set-profile', [AuthController::class, 'openProfilePage'])->name('auth.setProfile');
Route::post('login', [AuthController::class, 'authenticate'])->name('auth.authenticate');
Route::post('register', [AuthController::class, 'saveRegister'])->name('auth.register.submit'); 

//dashboard
Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(CheckLoginStatus::class) 
    ->name('panel.dashboard');


    

    //profile
Route::get('profile', [UserController::class, 'index'])
    ->middleware(CheckLoginStatus::class) 
    ->name('panel.profile');
Route::post('profile/update', [UserController::class, 'update'])
    ->middleware(CheckLoginStatus::class) 
    ->name('panel.profile.update');
Route::post('profile/change-password', [UserController::class, 'changePassword'])->name('panel.profile.changePassword');

// Route::get('account-settings', [UserController::class, 'accountSettings'])->name('panel.account.acountSettings');



    Route::get('/account-settings', [UserController::class, 'accountSettings'])->name('panel.account.acountSettings');
    Route::post('/account-settings/update', [UserController::class, 'updateAccount'])->name('panel.account.update');
    Route::get('/manage-account', [UserController::class, 'manageAccount'])->name('panel.account.manage');
    Route::post('/generate-account', [UserController::class, 'generateAccount'])->name('panel.account.generate');
    Route::post('/generate-account-administrator', [UserController::class, 'generateAdministrator'])->name('panel.account.generateAdministrator');
    Route::delete('/manage-account/{user}', [UserController::class, 'deleteUser'])->name('panel.account.deleteUser');
    Route::get('/delete-my-account', [UserController::class, 'deleteMyAccount'])->name('panel.account.deleteMyAccount');
    Route::delete('/delete-my-account', [UserController::class, 'destroyMyAccount'])->name('panel.account.destroyMyAccount');




//device
Route::get('device', [DeviceController::class, 'index'])->name('panel.device')->middleware(CheckLoginStatus::class);
Route::post('device/store', [DeviceController::class, 'store'])->name('panel.device.store')->middleware(CheckLoginStatus::class);
Route::post('device/update', [DeviceController::class, 'update'])->name('panel.device.update')->middleware(CheckLoginStatus::class);
// routes/web.php
Route::post('device/destroy/{id}', [DeviceController::class, 'destroy'])->name('panel.device.destroy')->middleware(CheckLoginStatus::class);

Route::get('device/{id}', [DeviceController::class, 'getDeviceData']); // Route to fetch device data for update
Route::post('device/bulkDestroy', [DeviceController::class, 'bulkDestroy'])->name('panel.device.bulkDestroy')->middleware(CheckLoginStatus::class); // Route baru untuk bulk delete
Route::post('/device/bulk-destroy-duplicates', [DeviceController::class, 'bulkDestroyFromDuplicates'])->name('panel.device.bulkDestroyFromDuplicates');

//stored device
Route::get('stored-device', [StoredDeviceController::class, 'index'])->name('panel.stored-device')->middleware(CheckLoginStatus::class);
Route::post('stored-device/store', [StoredDeviceController::class, 'store'])->name('panel.stored-device.store')->middleware(CheckLoginStatus::class);
Route::post('stored-device/update', [StoredDeviceController::class, 'update'])->name('panel.stored-device.update')->middleware(CheckLoginStatus::class);
Route::delete('stored-device/destroy/{id}', [StoredDeviceController::class, 'destroy'])->name('panel.stored-device.destroy')->middleware(CheckLoginStatus::class);
Route::post('stored-device/bulkDestroy', [StoredDeviceController::class, 'bulkDestroy'])->name('panel.stored-device.bulkDestroy')->middleware(CheckLoginStatus::class); // Route baru untuk bulk delete
Route::get('stored-device/{id}', [StoredDeviceController::class, 'getStoredDeviceData']); // Route to fetch device data for update



Route::get('client', [ClientController::class, 'index'])->name('panel.client')->middleware(CheckLoginStatus::class);
Route::post('client/store', [ClientController::class, 'store'])->name('panel.client.store')->middleware(CheckLoginStatus::class);
Route::post('client/update', [ClientController::class, 'update'])->name('panel.client.update')->middleware(CheckLoginStatus::class);
Route::delete('client/destroy/{id}', [ClientController::class, 'destroy'])->name('panel.client.destroy')->middleware(CheckLoginStatus::class);
Route::post('client/bulkDestroy', [ClientController::class, 'bulkDestroy'])->name('panel.client.bulkDestroy')->middleware(CheckLoginStatus::class); // Route baru untuk bulk delete
Route::get('client/{id}', [ClientController::class, 'getStoredClientData']); // Route to fetch device data for update
Route::post('/client/bulk-destroy-duplicates', [ClientController::class, 'bulkDestroyFromDuplicates'])->name('panel.client.bulkDestroyFromDuplicates');



//for user client
Route::get('dashboard-user', [DashboardController::class, 'indexClient'])
->middleware(CheckLoginStatus::class) 
->name('panel.dashboard.user');
Route::get('ticket', [TicketController::class, 'userTicket'])
->middleware(CheckLoginStatus::class) 
->name('panel.ticket.user-ticket');
Route::get('ticket/admin', [TicketController::class, 'adminTicketIndex'])
->middleware(CheckLoginStatus::class) 
->name('panel.ticket.admin-ticket');
Route::get('ticket/master', [TicketController::class, 'masterTicketIndex'])
->middleware(CheckLoginStatus::class) 
->name('panel.ticket.master-ticket');
Route::get('message', [MessageController::class, 'index'])
->middleware(CheckLoginStatus::class) 
->name('panel.message.user-message');

Route::post('tickets/client', [TicketController::class, 'store'])->name('panel.ticket.store')->middleware(CheckLoginStatus::class);
Route::post('/tickets/client-cancel', [TicketController::class, 'cancel'])->name('panel.ticket.cancel'); // Nama route seperti di form

Route::get('/admin/users/{user}/tickets', [TicketController::class, 'showUserTickets'])->name('admin.user.tickets.show');
// web.php
// Route Anda sudah OK
Route::post('/tickets/{id}/accept', [TicketController::class, 'accept'])->name('ticket.accept');
Route::post('/tickets/{id}/reject', [TicketController::class, 'reject'])->name('ticket.reject');

Route::post('/message/client-message', [MessageController::class, 'userMessage'])->name('panel.chat.send'); // Nama route seperti di form
Route::get('/chat',     [MessageController::class, 'index'])->name('chat.index');
Route::get('/chat', [MessageController::class, 'adminIndex'])->name('chat.index');
Route::get('/panel/admin/chat/conversation/{userId}', [MessageController::class, 'showConversation'])->name('chat.show'); // Untuk AJAX get messages
Route::post('/chat/send', [MessageController::class, 'sendMessage'])->name('panel.admin.chat.send'); // Untuk AJAX send message
Route::post('/panel/admin/chat/mark-as-read', [MessageController::class, 'markMessagesAsRead'])->name('panel.admin.chat.markAsRead');
Route::post('/panel/user/messages/mark-as-read', [MessageController::class, 'markMessagesFromAdminAsRead'])->name('panel.user.messages.markAsRead');
Route::get('/delete.chat', [MessageController::class, 'deleteChat'])->name('delete.chat');

Route::get('/histories', [HistoryController::class, 'index'])->name('histories.index');
Route::post('/histories', [HistoryController::class, 'store'])->name('histories.store');
Route::get('/histories/show', [HistoryController::class, 'show'])->name('histories.show');

Route::post('/letters/generate-sst', [LettersController::class, 'generateSst'])->name('admin.letters.generateSst');
Route::get('/letter', [LettersController::class, 'index'])->name('admin.letter.view');
Route::post('/letter', [LettersController::class, 'index'])->name('admin.letter.store');
Route::delete('/panel/letters/{letter}/delete', [LettersController::class, 'softDelete'])->name('panel.letter.softDelete');
       // routes/web.php

Route::get('/letters/{letter}/view-archive', [LettersController::class, 'viewArchivedPdf'])->name('panel.letter.view_archive');
Route::get('/letters/{letter}/view-archive-signed', [LettersController::class, 'viewSignedArchive'])->name('panel.letter.view_signed_archive');
Route::get('/letters/{letter}/download-archive', [LettersController::class, 'downloadArchivedPdf'])->name('panel.letter.download_archive');
Route::post('/letters/store-with-devices', [LettersController::class, 'storeWithDevices'])->name('panel.letter.storeWithDevices');
// ...
        

Route::get('/deployments', [DeploymentDeviceController::class, 'index'])->name('admin.deployment.view');
// routes/web.php atau file route yang relevan

// Pastikan route ini berada dalam grup middleware yang sama dengan route lainnya
Route::get('/asset-flow', [TransactionController::class, 'index'])->name('admin.asset-flow.view');
Route::get('/api/get-deployed-devices/{user_id}', [TransactionController::class, 'getPreviousDeployment'])->name('admin.transaction.getDeployDevice');

// ---- ROUTE BARU UNTUK PROSES TRANSAKSI ----
Route::post('/transaction/from-letter', [TransactionController::class, 'processTransactionFromLetter'])->name('admin.transaction.processFromLetter');
Route::post('/transaction/manual/other-source', [TransactionController::class, 'processTransactionManualOtherSource'])->name('admin.transaction.processManualOtherSource');
Route::post('/transaction/manual/selected-client', [TransactionController::class, 'processTransactionManualSelectedClient'])->name('admin.transaction.processManualSelectedClient');
Route::post('/transaction/manual/deployed', [TransactionController::class, 'processTransactionManualDeployed'])->name('admin.transaction.processManualDeployed');
Route::get('/api/other-source/search', [TransactionController::class, 'searchOtherSource'])->name('api.otherSource.search');
Route::post('/complete-letter-submission', [TransactionController::class, 'processSubmission'])->name('letter.process.completion');
Route::get('/access-transaction/{token}', [TransactionController::class, 'accessTransaction'])->name('transaction.access');
// routes/web.php


Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/generate', [ReportController::class, 'generateReport'])->name('reports.generate');
    Route::get('/download-pdf', [ReportController::class, 'downloadPdf'])->name('reports.downloadPdf');
    Route::post('/print-pdf', [ReportController::class, 'printPdf'])->name('reports.printPdf'); // <-- TAMBAHAN INI
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('reports.exportExcel');
    // routes/web.php

// ... route Anda yang lain ...
Route::get('/reports/view-printable-pdf', [ReportController::class, 'viewPrintablePdf'])->name('reports.viewPrintable');
});