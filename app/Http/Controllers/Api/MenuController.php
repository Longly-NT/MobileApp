<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
class MenuController extends Controller
{
    public function index()
    {
        $categories = Category::with(['menuItems' => fn ($q) => $q->where('is_available', true)])
            ->orderBy('name')
            ->get();

        return response()->json(
            $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'station' => $category->station,
                    'items' => $category->menuItems->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'description' => $item->description,
                            'allergy_info' => $item->allergy_info,
                            'price' => (float) $item->price,
                            'image' => $item->image ? Storage::disk('supabase')->url($item->image) : null,
                        ];
                    }),
                ];
            })
        );
    }
}