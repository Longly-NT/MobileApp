<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dailySummary(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $summary = Payment::summaryForDate($date);

        $byPaymentMethod = Payment::whereDate('created_at', $date)
            ->get()
            ->groupBy('payment_method')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'gross' => (float) $group->sum('subtotal_amount'),
                'net' => (float) ($group->sum('total_amount') - $group->sum('refund_amount')),
            ]);

        return response()->json([
            'date' => $summary['date'],
            'gross_sales' => (float) $summary['gross_sales'],
            'transaction_count' => (int) $summary['transaction_count'],
            'net_sales' => (float) $summary['net_sales'],
            'discounts' => (float) $summary['discounts'],
            'tax_collected' => (float) $summary['tax_collected'],
            'tips' => (float) $summary['tips'],
            'by_payment_method' => $byPaymentMethod->map(function ($data, $method) {
                return [
                    'method' => $method,
                    'count' => $data['count'],
                    'gross' => $data['gross'],
                    'net' => $data['net'],
                ];
            })->values(),
        ]);
    }
}