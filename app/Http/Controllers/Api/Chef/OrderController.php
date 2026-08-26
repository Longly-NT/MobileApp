<?php

namespace App\Http\Controllers\Api\Chef;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $pending = Order::with(['diningTable', 'items.menuItem'])
            ->where('status', 'sent_to_kitchen')
            ->oldest('sent_to_kitchen_at')
            ->get();

        $active = Order::with(['diningTable', 'items.menuItem'])
            ->whereIn('status', ['accepted', 'preparing'])
            ->oldest('accepted_at')
            ->get();

        $finished = Order::with(['diningTable', 'items.menuItem'])
            ->where('status', 'finished')
            ->latest('finished_at')
            ->take(10)
            ->get();

        return response()->json([
            'pending' => $pending->map(fn ($o) => $this->formatOrder($o)),
            'active' => $active->map(fn ($o) => $this->formatOrder($o)),
            'finished' => $finished->map(fn ($o) => $this->formatOrder($o)),
        ]);
    }

    public function accept(Order $order)
    {
        abort_unless($order->status === 'sent_to_kitchen', 422, 'Order is not awaiting acceptance.');

        $order->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json($this->formatOrder($order));
    }

    public function preparing(Order $order)
    {
        abort_unless($order->status === 'accepted', 422, 'Order must be accepted first.');

        $order->update(['status' => 'preparing']);

        return response()->json($this->formatOrder($order));
    }

    public function finished(Order $order)
    {
        abort_unless($order->status === 'preparing', 422, 'Order must be preparing first.');

        $order->update([
            'status' => 'finished',
            'finished_at' => now(),
        ]);

        return response()->json($this->formatOrder($order));
    }

    private function formatOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'table_name' => $order->diningTable->name,
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->menuItem->name,
                'quantity' => $item->quantity,
            ]),
        ];
    }
}