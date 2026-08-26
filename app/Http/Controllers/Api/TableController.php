<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiningTable;

class TableController extends Controller
{
    public function index()
    {
        $tables = DiningTable::with(['activeOrder'])->orderBy('name')->get();

        return response()->json(
            $tables->map(function ($table) {
                return [
                    'id' => $table->id,
                    'name' => $table->name,
                    'capacity' => $table->capacity,
                    'status' => $table->status,
                    'active_order_id' => $table->activeOrder?->id,
                    'active_order_status' => $table->activeOrder?->status,
                ];
            })
        );
    }
}