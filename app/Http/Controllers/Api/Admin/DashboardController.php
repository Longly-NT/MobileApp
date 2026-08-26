<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'staff_count' => User::whereIn('role', ['staff', 'chef'])->count(),
            'menu_items' => MenuItem::count(),
            'tables' => DiningTable::count(),
            'active_orders' => Order::whereNotIn('status', ['paid', 'cancelled'])->count(),
        ];

        $recentOrders = Order::with(['diningTable', 'user'])->latest()->take(10)->get();

        $todaySales = Payment::summaryForDate(now()->toDateString());

        return response()->json([
            'greeting_name' => explode(' ', auth()->user()->name)[0],
            'stats' => $stats,
            'today_sales' => $todaySales,
            'recent_orders' => $recentOrders->map(fn ($order) => [
                'id' => $order->id,
                'table_name' => $order->diningTable->name,
                'staff_name' => $order->user->name,
                'status' => $order->status,
                'total' => (float) $order->total,
                'created_at' => $order->created_at->diffForHumans(),
            ]),
        ]);
    }
}