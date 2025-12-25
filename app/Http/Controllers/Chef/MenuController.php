<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Cuisine;
use App\Models\DietaryPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct()
    {
        // 1. Security: Only allow Chefs
        // 'verified' middleware ensures they have completed their profile
        $this->middleware(['auth', 'role:chef', 'verified']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $chef = Auth::user();

        // 2. Performance: Eager load relationships to prevent N+1 query issues
        $menus = $chef->Menu->menus()
            ->with(['category', 'cuisines'])
            ->latest()
            ->paginate(12);

        // 3. Stats for the Dashboard View
        $stats = [
            'total' => $chef->Menu->menus()->count(),
            'active' => $chef->Menu->menus()->where('is_available', true)->count(),
            'featured' => $chef->Menu->menus()->where('is_featured', true)->count(),
        ];

        return view('chefs.menu.index', compact('menus', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 4. Populate Dropdowns: Fetch data from our new Models
        $categories = Category::orderBy('name')->get();
        $cuisines = Cuisine::orderBy('name')->get();
        $dietaries = DietaryPreference::orderBy('name')->get();

        return view('chefs.menu.create', compact('categories', 'cuisines', 'dietaries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 5. Validation
        $validated = $this->validateMenuData($request);

        // 6. Handle Images
        $validated['images'] = $this->handleImageUploads($request);

        // 7. Set Owner
        $validated['chef_id'] = Auth::id();

        // 8. Create Menu (Mass Assignment)
        $menu = Menu::create($validated);

        // 9. Sync Relationships (The Core "Pivot" Logic)
        if (!empty($request->cuisine_ids)) {
            $menu->cuisines()->sync($request->cuisine_ids);
        }
        if (!empty($request->dietary_ids)) {
            $menu->dietaryPreferences()->sync($request->dietary_ids);
        }

        return redirect()->route('chef.menus.index')
            ->with('success', 'Menu item created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        $this->authorize('view', $menu);
        // Load all related data for the detail view
        $menu->load(['category', 'cuisines', 'dietaryPreferences', 'reviews']);

        return view('chefs.menu.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $this->authorize('update', $menu);

        // Fetch data for dropdowns again
        $categories = Category::orderBy('name')->get();
        $cuisines = Cuisine::orderBy('name')->get();
        $dietaries = DietaryPreference::orderBy('name')->get();

        return view('chefs.menu.edit', compact('menu', 'categories', 'cuisines', 'dietaries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);

        $validated = $this->validateMenuData($request);

        // 10. Handle Image Updates
        if ($request->hasFile('images')) {
            $validated['images'] = $this->handleImageUploads($request, $menu);
        }

        // 11. Update Base Data
        $menu->update($validated);

        // 12. Sync Relationships (Attach/Detach automatically)
        $menu->cuisines()->sync($request->input('cuisine_ids', []));
        $menu->dietaryPreferences()->sync($request->input('dietary_ids', []));

        return redirect()->route('chef.menus.index')
            ->with('success', 'Menu updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $this->authorize('delete', $menu);
        $menu->delete(); // Soft Delete

        return redirect()->route('chef.menus.index')
            ->with('success', 'Menu item moved to trash.');
    }

    // --- AJAX ACTIONS (Toggles) ---

    public function toggleAvailability(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);

        $menu->update(['is_available' => $request->boolean('is_available')]);

        return response()->json([
            'success' => true,
            'message' => $menu->is_available ? 'Item is now available' : 'Item is unavailable'
        ]);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'menu_ids' => 'required|array',
            'menu_ids.*' => 'exists:menus,id',
            'action' => 'required|in:make_available,make_unavailable,delete'
        ]);

        $ids = $request->menu_ids;

        // Security: Ensure user owns these menus
        $count = Menu::whereIn('id', $ids)->where('chef_id', Auth::id())->count();
        if ($count !== count($ids)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        // Perform Action
        switch ($request->action) {
            case 'make_available':
                Menu::whereIn('id', $ids)->update(['is_available' => true]);
                break;
            case 'make_unavailable':
                Menu::whereIn('id', $ids)->update(['is_available' => false]);
                break;
            case 'delete':
                Menu::whereIn('id', $ids)->delete();
                break;
        }

        return response()->json(['success' => true, 'message' => 'Bulk action applied.']);
    }

    // --- HELPER METHODS ---

    private function validateMenuData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|lt:price',

            // Relationships Validation
            'category_id' => 'required|exists:categories,id',
            'cuisine_ids' => 'nullable|array',
            'cuisine_ids.*' => 'exists:cuisines,id',
            'dietary_ids' => 'nullable|array',
            'dietary_ids.*' => 'exists:dietary_preferences,id',

            'preparation_time_minutes' => 'nullable|integer|min:1',
            'serves_count' => 'required|integer|min:1',
            'spice_level' => 'nullable|integer|between:0,5',
            'stock_quantity' => 'nullable|integer|min:0',

            // JSON Fields
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'string',
            'allergens' => 'nullable|array',
            'allergens.*' => 'string',

            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
    }

    private function handleImageUploads(Request $request, Menu $existingMenu = null): array
    {
        // If updating and no new images sent, preserve old ones
        if (!$request->hasFile('images')) {
            return $existingMenu ? ($existingMenu->images ?? []) : [];
        }

        $uploadedImages = [];
        // Store new images
        foreach ($request->file('images') as $image) {
            $filename = 'menu_' . time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('menu-images', $filename, 'public');
            $uploadedImages[] = $path;
        }

        return $uploadedImages;
    }
}
