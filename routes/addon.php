<?php

use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\Owner\BankController;
use App\Http\Controllers\Owner\CostController;
use App\Http\Controllers\Owner\ProductController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\Super\RolePermissionController;
use App\Http\Controllers\Super\UserManagementController;
use App\Http\Controllers\Super\TermController;
use App\Http\Controllers\Super\TicketController;
use App\Http\Controllers\TicketController as TicketUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BuyPointController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {

    Route::middleware(['permission:product-crud'])->group(function() {
        Route::patch('product/{product}/activating', [ProductController::class, 'activating'])->name('product.activating');
        Route::resource('product', ProductController::class)->except(['edit', 'create'])->missing(function () {
            return redirect(route('product.index'));
        });
    });

    Route::prefix('management')->middleware(['role:super_admin'])->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('management.users');
        Route::get('/user-detail/{user}', [RolePermissionController::class, 'show'])->name('user-detail');

        Route::prefix('role')->name('role.')->group(function() {
            Route::get('/get/{role}', [RolePermissionController::class, 'getRole'])->name('get');
            Route::post('/assign', [RolePermissionController::class, 'assignRole'])->name('assign');
            Route::post('/add', [RolePermissionController::class, 'addRole'])->name('add');
            Route::patch('/update/{role}', [RolePermissionController::class, 'assignPermissions'])->name('update');
            Route::delete('/delete/{role}', [RolePermissionController::class, 'deleteRole'])->name('destroy');
        });

        Route::prefix('permission')->name('permission.')->group(function() {
            Route::get('/get/{permission}', [RolePermissionController::class, 'getPermission'])->name('get');
            Route::post('/add', [RolePermissionController::class, 'addPermission'])->name('add');
            Route::patch('/update/{permission}', [RolePermissionController::class, 'updatePermission'])->name('update');
            Route::delete('/delete/{permission}', [RolePermissionController::class, 'deletePermission'])->name('destroy');
        });

        Route::post('/add-permission', [RolePermissionController::class, 'addPermission'])->name('management.add-permission');

        Route::get('users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('users/transaction/{user}', [UserManagementController::class, 'transaction'])->name('users.transaction');
        Route::get('users/point/{user}', [UserManagementController::class, 'point'])->name('users.point');

        Route::resource('cost', CostController::class)->except(['edit', 'create'])->missing(function () {
            return redirect(route('cost.index'));
        });
    });

    // Transaction
        Route::group(['prefix' => 'transaction'], function(){
            Route::get('/', [TransactionController::class, 'index'])->name('transaction.index');
            Route::get('/{transaction:invoice}', [TransactionController::class, 'show'])->name('transaction.show');
            Route::post('/', [TransactionController::class, 'store'])->name('transaction.store');
            Route::patch('/confirmation/{transaction}', [TransactionController::class, 'confirmation'])->name('transaction.confirmation');

            Route::group(['middleware' => 'permission:update-transaction'], function(){
                Route::patch('/status/{transaction}', [TransactionController::class, 'status'])->name('transaction.status');
            });
            Route::patch('/cancel/{transaction}', [TransactionController::class, 'cancel'])->name('transaction.cancel');
        });
    // Transaction
    // Bank
        Route::patch('bank/status/{bank}', [BankController::class, 'status'])->name('bank.status');
        Route::resource('bank', BankController::class);
    // Bank
    // Point
        Route::get('point', [PointController::class, 'index'])->name('point.index');
    // Point
    // Campaign	
        Route::get('campaign/type', [CampaignController::class, 'type'])->name('campaign.type');
        Route::get('campaign/template/{campaign}', [CampaignController::class, 'type'])->name('campaign.template');
        // Laravel Queue
            Route::post('campaign/{campaign}/sendByJob', [CampaignController::class, 'sendByJob'])->name('campaign.sendByJob');
        // Laravel Queue
        // Asynchronous JS
            Route::post('campaign/{campaign}/send', [CampaignController::class, 'action'])->name('campaign.send');
        // Asynchronous JS
        Route::get('campaign/{campaign}/history', [CampaignController::class, 'history'])->name('campaign.history');
        Route::resource('campaign', CampaignController::class);
    // Campaign
    
    // Buy Point
    Route::resource('buypoint', BuyPointController::class);
    Route::get('/p/{product:slug}', [BuyPointController::class, 'detail'])->name('buypoint.detail');
    // Buy Point
    // Term
        Route::resource('term', TermController::class);
    // Term
    // Ticket
        Route::resource('tickets', TicketController::class);
    // Ticket
    
    // Change Password
        Route::get('change-password', [PasswordChangeController::class, 'index'])->name('change-password');
        Route::patch('change-password', [PasswordChangeController::class, 'store'])->name('change-password.update');
    // Change Password
    
    // Fetch Group
        Route::get('group/{number}', [GroupController::class, 'show'])->name('show-group');
        Route::get('fetch-group/{sender}', [GroupController::class, 'fetch'])->name('fetch-group');
        Route::get('fetch-participants/{sender}/{jid}', [GroupController::class, 'participant'])->name('participants-group');
    // Fetch Group
    // Message
        Route::get('/message/test', [MessagesController::class, 'index'])->name('messagetest');
        Route::post('/message/test/text', [MessagesController::class, 'textMessageTest'])->name('textMessageTest');
        Route::post('/message/test/image', [MessagesController::class, 'mediaMessageTest'])->name('mediaMessageTest');
        Route::post('/message/test/button', [MessagesController::class, 'buttonMessageTest'])->name('buttonMessageTest');
        Route::post('/message/test/template', [MessagesController::class, 'templateMessageTest'])->name('templateMessageTest');
        Route::post('/message/test/location', [MessagesController::class, 'locationMessageTest'])->name('locationMessageTest');
    // Message
    // Inbox
        Route::get('inboxes', [InboxController::class, 'index'])->name('inbox.index');
        Route::get('inbox/{number}', [InboxController::class, 'show'])->name('inbox.show');
        Route::delete('inbox/{number}/{id}', [InboxController::class, 'destroy'])->name('inbox.delete');
    // Inbox
    // Role Customer
        // Ticket
        Route::resource('ticket', TicketUserController::class);
        // Ticket
    // Role Customer
});