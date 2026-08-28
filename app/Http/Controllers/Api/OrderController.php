<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Open (or resume) an order for a table — same logic as the web version,
     * just returns JSON instead of a redirect.
     */
    public function openForTable(DiningTable $table)
    {
        $order = $table->orders()->whereNotIn('status', ['paid', 'cancelled'])->latest()->first();

        if (! $order) {
            $order = Order::create([
                'dining_table_id' => $table->id,
                'user_id' => Auth::id(),
                'status' => 'open',
            ]);

            $table->update(['status' => 'occupied']);
        }

        return $this->showOrder($order);
    }

    public function show(Order $order)
    {
        return $this->showOrder($order);
    }

    private function showOrder(Order $order)
    {
        $order->load(['items.menuItem', 'diningTable']);

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'table_name' => $order->diningTable->name,
            'total' => (float) $order->total,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'menu_item_id' => $item->menu_item_id,
                    'name' => $item->menuItem->name,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                ];
            }),
        ]);
    }
        public function addItem(\Illuminate\Http\Request $request, Order $order)
    {
        $this->guardOpenForItems($order);

        $data = $request->validate([
            'menu_item_id' => ['required', 'exists:menu_items,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $menuItem = \App\Models\MenuItem::findOrFail($data['menu_item_id']);
        $quantity = $data['quantity'] ?? 1;

        $existing = $order->items()
            ->where('menu_item_id', $menuItem->id)
            ->whereNull('notes')
            ->first();

        if ($existing) {
            $existing->increment('quantity', $quantity);
        } else {
            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'price' => $menuItem->price,
            ]);
        }

        $order->recalculateTotal();

        return $this->showOrder($order);
    }

    public function sendToKitchen(Order $order)
    {
        abort_unless($order->status === 'open', 422, 'This order can no longer be edited.');
        abort_if($order->items()->count() === 0, 422, 'Add at least one item before sending to the kitchen.');

        $order->update([
            'status' => 'sent_to_kitchen',
            'sent_to_kitchen_at' => now(),
        ]);

        return $this->showOrder($order);
    }

    private function guardOpenForItems(Order $order): void
    {
        abort_unless(
            in_array($order->status, ['open', 'sent_to_kitchen']),
            422,
            'This order is already being prepared in the kitchen and can no longer be edited.'
        );
    }
        public function markServed(Order $order)
    {
        abort_unless($order->status === 'finished', 422, 'Order must be finished by the kitchen first.');

        $order->update([
            'status' => 'served',
            'served_at' => now(),
        ]);

        return $this->showOrder($order);
    }

    public function recordPayment(\Illuminate\Http\Request $request, Order $order)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,card,mobile'],
            'tip_amount' => ['nullable', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $orderSubtotal = (float) $order->total;
        $discountPercent = $data['discount_percent'] ?? 0.00;
        $discount = round($orderSubtotal * ($discountPercent / 100), 2);
        $tip = $data['tip_amount'] ?? 0.00;

        $taxableAmount = max($orderSubtotal - $discount, 0);
        $taxRate = config('pos.tax_rate', 0);
        $tax = round($taxableAmount * $taxRate, 2);

        $totalToCollect = round($orderSubtotal - $discount + $tax + $tip, 2);

        if (round($data['amount'], 2) < $totalToCollect) {
            return response()->json([
                'message' => 'Amount is less than the total due.',
                'total_due' => $totalToCollect,
            ], 422);
        }

        $order->payments()->create([
            'subtotal_amount' => $orderSubtotal,
            'tendered_amount' => round($data['amount'], 2),
            'discount_amount' => $discount,
            'discount_percent' => $discountPercent,
            'tax_amount' => $tax,
            'tip_amount' => $tip,
            'total_amount' => $totalToCollect,
            'payment_method' => $data['method'],
            'processed_by' => auth()->id(),
            'refund_amount' => 0.00,
        ]);

        if ($order->balanceDue() <= 0) {
            $order->update(['status' => 'paid']);
            $order->diningTable->update(['status' => 'available']);
        }

        return $this->showOrder($order);
    }
        public function updateItemQuantity(\Illuminate\Http\Request $request, Order $order, \App\Models\OrderItem $item)
    {
        $this->guardOpenForItems($order);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $item->update(['quantity' => $data['quantity']]);
        $order->recalculateTotal();

        return $this->showOrder($order);
    }

    public function removeItem(Order $order, \App\Models\OrderItem $item)
    {
        $this->guardOpenForItems($order);

        $item->delete();
        $order->recalculateTotal();

        return $this->showOrder($order);
    }
}