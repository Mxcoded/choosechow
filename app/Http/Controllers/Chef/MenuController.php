<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the menu items.
     */
    public function index()
    {
        // Get only this Chef's menus, ordered by newest
        $menus = Menu::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('chef.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        return view('chef.menus.create');
    }

    /**
     * Store a newly created menu item in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string', // Changed from category_id to string 'category' for simplicity
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
            'preparation_time' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['is_available'] = $request->has('is_available'); // Handle checkbox

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        Menu::create($data);

        return redirect()->route('chef.menus.index')->with('success', 'Menu item added successfully!');
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit(Menu $menu)
    {
        // Security: Ensure chef owns this menu item
        // if ($menu->user_id !== Auth::id()) {
        //     abort(403);
        // }
        return view('chef.menus.edit', compact('menu'));
    }

    /**
     * Update the specified menu item in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        if ($menu->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preparation_time' => 'nullable|integer|min:0',
        ]);

        // Only get the fields we want to update (exclude image from mass assignment)
        $data = $request->only(['name', 'description', 'price', 'category', 'preparation_time']);
        $data['is_available'] = $request->has('is_available');

        // Handle Image Replacement - only if a new file is uploaded
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            // Store new image
            $data['image'] = $request->file('image')->store('menus', 'public');
        }

        $menu->update($data);

        return redirect()->route('chef.menus.index')->with('success', 'Menu item updated successfully.');
    }

    /**
     * Remove the specified menu item from storage.
     */
    public function destroy(Menu $menu)
    {
        if ($menu->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete image file to save space
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        return redirect()->route('chef.menus.index')->with('success', 'Dish deleted.');
    }

    /**
     * Toggle "In Stock" / "Sold Out" via AJAX or Button
     */
    public function toggleAvailability(Menu $menu)
    {
        if ($menu->user_id !== Auth::id()) {
            abort(403);
        }

        $menu->is_available = !$menu->is_available;
        $menu->save();

        $status = $menu->is_available ? 'Available' : 'Sold Out';
        return back()->with('success', "Item marked as $status.");
    }
    
    /**
     * Toggle "Featured" Status (Optional)
     */
    public function toggleFeatured(Menu $menu)
    {
        if ($menu->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Assuming you have 'is_featured' column. If not, ignore this.
        // $menu->is_featured = !$menu->is_featured;
        // $menu->save();
        
        return back();
    }
    
    /**
     * Bulk Update (Optional - for bulk actions in table)
     */
    public function bulkUpdate(Request $request)
    {
         // Placeholder for future bulk actions
         return back();
    }
}