@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
    <h3 class="mb-3">My Active Orders</h3>

    <div class="card shadow-sm">
        <div class="table-responsive" style="border-radius: var(--radius); overflow: hidden;">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Table</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->diningTable->name }}</td>
                                        <td><span class="badge {{ match ($order->status) {
                            'open' => 'bg-secondary',
                            'sent_to_kitchen', 'accepted', 'preparing' => 'bg-warning',
                            'finished' => 'bg-info',
                            'served' => 'bg-success',
                            'paid' => 'bg-dark',
                            default => 'bg-secondary',
                        } }} text-uppercase">{{ str_replace('_', ' ', $order->status) }}</span></td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                        <td class="text-end"><a href="{{ route('staff.orders.show', $order) }}"
                                                class="btn btn-sm btn-outline-dark">Open</a></td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No active orders. <a
                                    href="{{ route('staff.tables.index') }}">Select a table</a> to start one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection