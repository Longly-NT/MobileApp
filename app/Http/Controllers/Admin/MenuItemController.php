<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    /**
     * The description field is edited with a small rich-text toolbar (bold,
     * italic, underline, bullet/numbered lists). Strip everything down to that
     * whitelist before it ever touches the database, so no other HTML/JS can
     * be injected through the field.
     */
    protected function sanitizeDescription(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $allowedTags = '<b><strong><i><em><u><ul><ol><li><br><p>';
        $clean = strip_tags($html, $allowedTags);

        return trim($clean) === '' ? null : $clean;
    }

    public function index()
    {
        $menuItems = MenuItem::with('category')->latest()->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.menu-items.index', compact('menuItems', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'allergy_info' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ]);

        $data['description'] = $this->sanitizeDescription($data['description'] ?? null);
        $data['is_available'] = true;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('', 'supabase');
            $data['image'] = $imagePath;
        }

        MenuItem::create($data);

        return back()->with('status', 'Menu item added.');
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'allergy_info' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
        ]);

        $data['description'] = $this->sanitizeDescription($data['description'] ?? null);

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if (!empty($menuItem->image) && $menuItem->image !== null) {
                try {
                    Storage::disk('public')->delete($menuItem->image);
                } catch (\Exception $e) {
                    // Log but don't fail if old image can't be deleted
                    Log::warning('Failed to delete old image: ' . $e->getMessage());
                }
            }
            // Store new image
            $imagePath = $request->file('image')->store('menu-items', 'public');
            $data['image'] = $imagePath;
        }

        $menuItem->update($data);

        return back()->with('status', 'Menu item updated.');
    }

    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => ! $menuItem->is_available]);

        return back()->with('status', 'Availability updated.');
    }

    public function destroy(MenuItem $menuItem)
    {
        if (!empty($menuItem->image) && $menuItem->image !== null) {
            try {
                Storage::disk('public')->delete($menuItem->image);
            } catch (\Exception $e) {
                // Log but don't fail if image can't be deleted
                Log::warning('Failed to delete image: ' . $e->getMessage());
            }
        }
        $menuItem->delete();

        return back()->with('status', 'Menu item removed.');
    }
}
