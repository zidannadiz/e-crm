<?php

use App\Http\Controllers\Ecrm\ClientController;
use App\Http\Controllers\Ecrm\InvoiceController;
use App\Http\Controllers\Ecrm\PaymentController;
use App\Http\Controllers\Ecrm\DashboardController;
use App\Http\Controllers\Ecrm\OrderController;
use App\Http\Controllers\Ecrm\ChatController;
use App\Http\Controllers\Ecrm\QuickReplyController;
use App\Http\Controllers\Ecrm\MessageController;
use App\Http\Controllers\Ecrm\CustomerServiceController;
use App\Http\Controllers\Ecrm\AdminController;
use App\Http\Controllers\Ecrm\ChatLoadBalancerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'web'])->prefix('ecrm')->name('ecrm.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Client routes (must be before admin routes to avoid conflict)
    // IMPORTANT: orders/create and orders/{order}/edit must be before orders/{order} to avoid route conflict
    Route::middleware('role:client')->group(function () {
        Route::get('my-orders', [OrderController::class, 'myOrders'])->name('orders.my');
        Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('my-invoices', [InvoiceController::class, 'myInvoices'])->name('invoices.my');
    });
    
    // Customer Service routes - Must be before admin routes to avoid conflicts
    Route::middleware('role:cs')->group(function () {
        // Orders - view & update status only
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        
        // Clients - read only
        Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        
        // Invoices - read only + send reminder
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('invoices/{invoice}/remind', [InvoiceController::class, 'sendReminder'])->name('invoices.remind');
        
        // Payments - read only
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        
        // Quick Replies - full CRUD
        Route::get('quick-replies', [QuickReplyController::class, 'index'])->name('quick-replies.index');
        Route::get('quick-replies/create', [QuickReplyController::class, 'create'])->name('quick-replies.create');
        Route::post('quick-replies', [QuickReplyController::class, 'store'])->name('quick-replies.store');
        Route::get('quick-replies/{quick_reply}', [QuickReplyController::class, 'show'])->name('quick-replies.show');
        Route::get('quick-replies/{quick_reply}/edit', [QuickReplyController::class, 'edit'])->name('quick-replies.edit');
        Route::put('quick-replies/{quick_reply}', [QuickReplyController::class, 'update'])->name('quick-replies.update');
        Route::delete('quick-replies/{quick_reply}', [QuickReplyController::class, 'destroy'])->name('quick-replies.destroy');
    });
    
    // Messages - inbox for Admin and CS (access control in controller)
    Route::middleware('role:admin|cs')->group(function () {
        Route::get('messages/inbox', [MessageController::class, 'inbox'])->name('messages.inbox');
        Route::post('messages/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
        Route::post('messages/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('messages.mark-all-read');
    });
    
    // Order edit/update/destroy routes - accessible by admin, cs, and client (access control in controller)
    // Must be before orders/{order} to avoid route conflict
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::post('orders/{order}/upload-desain', [OrderController::class, 'uploadDesain'])->name('orders.upload-desain');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    
    // Order show route - accessible by admin, cs, and client (access control in controller)
    // Must be after orders/create and orders/{order}/edit to avoid route conflict
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        // Clients - Admin has full CRUD (but CS routes are defined first, so CS can access index/show)
        Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
        Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        
        // Customer Service Management
        Route::resource('customer-services', CustomerServiceController::class);
        
        // Admin Management - Only accessible by admin@ecrm.com (Super Admin)
        Route::middleware('superadmin')->group(function () {
            Route::resource('admins', AdminController::class);
        });
        
        // Invoice routes - must be in specific order to avoid conflicts
        // IMPORTANT: invoices/create must be before invoices/{invoice} to avoid route conflict
        Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
        Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send'])->name('invoices.send');
        
        // Payments - Admin has full CRUD (but CS routes are defined first, so CS can access index/show)
        Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
        Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        
        // Orders - Admin specific routes (index is already defined for CS)
        Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        
        // Quick Replies - Admin also has full access (CS routes are defined first, so both can access)
        // No need to redefine, CS routes already cover all CRUD operations
    });
    
    // Invoice show route - accessible by admin, cs, and client (access control in controller)
    // Must be after invoices/create to avoid route conflict
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    
    // Chat routes - accessible by admin, cs, and client (access control in controller)
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('order/{order}', [ChatController::class, 'index'])->name('index');
        Route::post('order/{order}/send', [ChatController::class, 'send'])->name('send');
        Route::post('order/{order}/quick-reply', [ChatController::class, 'quickReply'])->name('quick-reply');
        Route::post('order/{order}/ai-answer', [ChatController::class, 'aiAnswer'])->name('ai-answer');
        Route::post('mark-read/{message}', [ChatController::class, 'markRead'])->name('mark-read');
        
        // Customer chat list
        Route::middleware('role:client')->group(function () {
            Route::get('my-chats', [ChatController::class, 'myChats'])->name('my-chats');
        });

        // Load balancer routes
        Route::prefix('load-balancer')->name('load-balancer.')->group(function () {
            Route::get('statistics', [ChatLoadBalancerController::class, 'getLoadStatistics'])->name('statistics');
            Route::post('order/{orderId}/end-session', [ChatLoadBalancerController::class, 'endSession'])->name('end-session');
            Route::post('order/{orderId}/reassign', [ChatLoadBalancerController::class, 'reassignAgent'])->name('reassign');
        });
    });
});

