<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $items = MenuItem::with('category')->latest()->get();

        return response()->json($items->map(fn($item) => $this->format($item)));
    }
    

    public function store(Request $request)
    {
        abort_unless($request->user()->role === 'admin', 403, 'Only Admin can add menu items.');

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        $data['is_available'] = true;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('', 'supabase');
        }

        $item = MenuItem::create($data);

        return response()->json($this->format($item), 201);
    }

     public function update(Request $request, MenuItem $menuItem)
    {
        abort_unless($request->user()->role === 'admin', 403, 'Only Admin can edit menu items.');

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        if ($request->hasFile('image')) {
            if (! empty($menuItem->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($menuItem->image);
            }
            $data['image'] = $request->file('image')->store('', 'supabase');
        }

        $menuItem->update($data);

        return response()->json($this->format($menuItem));
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Only Admin can change availability.');

        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return response()->json($this->format($menuItem));
    }

    public function destroy(MenuItem $menuItem)
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Only Admin can delete menu items.');

        $menuItem->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function format(MenuItem $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'price' => (float) $item->price,
            'is_available' => (bool) $item->is_available,
            'category_id' => $item->category_id,
            'category_name' => $item->category?->name,
            'image' => $item->image ? Storage::disk('supabase')->url($item->image) : null,
        ];
    }
        public function categories()
    {
        $categories = \App\Models\Category::orderBy('name')->get(['id', 'name']);

        return response()->json($categories);
    }
}
