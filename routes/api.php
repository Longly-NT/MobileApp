<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\Chef\OrderController as ChefOrderController;
use App\Http\Controllers\Api\Admin\MenuItemController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\DashboardController;


Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/tables', [TableController::class, 'index']);
    Route::get('/menu', [MenuController::class, 'index']);
    Route::post('/tables/{table}/open', [OrderController::class, 'openForTable']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/items', [OrderController::class, 'addItem']);
    Route::post('/orders/{order}/send-to-kitchen', [OrderController::class, 'sendToKitchen']);
    Route::get('/chef/orders', [ChefOrderController::class, 'index']);
    Route::post('/chef/orders/{order}/accept', [ChefOrderController::class, 'accept']);
    Route::post('/chef/orders/{order}/preparing', [ChefOrderController::class, 'preparing']);
    Route::post('/chef/orders/{order}/finished', [ChefOrderController::class, 'finished']);
    Route::post('/orders/{order}/serve', [OrderController::class, 'markServed']);
    Route::post('/orders/{order}/payments', [OrderController::class, 'recordPayment']);
    Route::get('/admin/menu-items', [MenuItemController::class, 'index']);
    Route::post('/admin/menu-items', [MenuItemController::class, 'store']);
    Route::put('/admin/menu-items/{menuItem}', [MenuItemController::class, 'update']);
    Route::patch('/admin/menu-items/{menuItem}/toggle', [MenuItemController::class, 'toggleAvailability']);
    Route::delete('/admin/menu-items/{menuItem}', [MenuItemController::class, 'destroy']);

    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::post('/admin/users', [AdminUserController::class, 'store']);
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update']);
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy']);

    Route::get('/admin/reports/daily', [ReportController::class, 'dailySummary']);
    Route::get('/admin/categories', [MenuItemController::class, 'categories']);
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
});
