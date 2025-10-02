<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'chef']);
    }

    public function index()
    {
        $chef = Auth::user();
        $menus = $chef->menus()->latest()->paginate(12);
        
        $stats = [
            'total_menus' => $chef->menus()->count(),
            'active_menus' => $chef->menus()->where('is_available', true)->count(),
            'featured_menus' => $chef->menus()->where('is_featured', true)->count(),
            'avg_price' => $chef->menus()->avg('price') ?? 0,
        ];

        return view('chefs.menu.index', compact('menus', 'stats'));
    }

    public function create()
    {
        $menu = new Menu(); // Empty model for form
        return view('chefs.menu.create', compact('menu'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateMenuData($request);
        $validated = $this->processMenuData($validated, $request);

        // Set creation-specific fields
        $validated['chef_id'] = Auth::id();
        $validated['slug'] = $this->generateSlug($validated['name']);
        $validated = array_merge($validated, $this->getDefaultTrackingFields());

        Menu::create($validated);

        $imageCount = is_array($validated['images']) ? count($validated['images']) : 0;
        return redirect()->route('chef.menus')->with('success', "Menu item created successfully with {$imageCount} images!");
    }

    public function show(Menu $menu)
    {
        $this->authorize('view', $menu);

        return view('chefs.menu.show', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        $this->authorize('update', $menu);
        return view('chefs.menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);

        $validated = $this->validateMenuData($request);
        $validated = $this->processMenuData($validated, $request, $menu);

        // Update slug if name changed
        if ($validated['name'] !== $menu->name) {
            $validated['slug'] = $this->generateSlug($validated['name']);
        }

        $menu->update($validated);

        return redirect()->route('chef.menus')->with('success', 'Menu item updated successfully!');
    }

    public function destroy(Menu $menu)
    {
        $this->authorize('delete', $menu);

        $this->deleteMenuImages($menu);
        $menu->delete();

        return redirect()->route('chef.menus')->with('success', 'Menu item and associated images deleted successfully!');
    }

    /**
     * Toggle menu item availability
     */
    public function toggleAvailability(Request $request, Menu $menu)
    {
        // Ensure the menu belongs to the authenticated chef
        if ($menu->chef_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'is_available' => 'required|boolean'
        ]);

        try {
            $menu->update([
                'is_available' => $request->is_available
            ]);

            return response()->json([
                'success' => true,
                'message' => $request->is_available ? 'Menu item is now available' : 'Menu item is now unavailable',
                'is_available' => $menu->is_available
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update availability'
            ], 500);
        }
    }

    /**
     * Toggle menu item featured status
     */
    public function toggleFeatured(Request $request, Menu $menu)
    {
        // Ensure the menu belongs to the authenticated chef
        if ($menu->chef_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'is_featured' => 'required|boolean'
        ]);

        try {
            $updateData = [
                'is_featured' => $request->is_featured
            ];

            // If marking as featured and no featured_until date exists, set it to 30 days from now
            if ($request->is_featured && !$menu->featured_until) {
                $updateData['featured_until'] = now()->addDays(30);
            }

            // If removing featured status, clear the featured_until date
            if (!$request->is_featured) {
                $updateData['featured_until'] = null;
            }

            $menu->update($updateData);

            return response()->json([
                'success' => true,
                'message' => $request->is_featured ? 'Menu item is now featured' : 'Menu item removed from featured',
                'is_featured' => $menu->is_featured,
                'featured_until' => $menu->featured_until ? $menu->featured_until->format('Y-m-d') : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update featured status'
            ], 500);
        }
    }

    /**
     * Bulk update menu items (for future use)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'menu_ids' => 'required|array',
            'menu_ids.*' => 'exists:menus,id',
            'action' => 'required|in:make_available,make_unavailable,make_featured,remove_featured,delete'
        ]);

        $menuIds = $request->menu_ids;
        $action = $request->action;

        // Ensure all menus belong to the authenticated chef
        $menus = Menu::whereIn('id', $menuIds)
            ->where('chef_id', Auth::id())
            ->get();

        if ($menus->count() !== count($menuIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Some menu items do not belong to you'
            ], 403);
        }

        try {
            switch ($action) {
                case 'make_available':
                    Menu::whereIn('id', $menuIds)->update(['is_available' => true]);
                    $message = 'Selected items are now available';
                    break;

                case 'make_unavailable':
                    Menu::whereIn('id', $menuIds)->update(['is_available' => false]);
                    $message = 'Selected items are now unavailable';
                    break;

                case 'make_featured':
                    Menu::whereIn('id', $menuIds)->update([
                        'is_featured' => true,
                        'featured_until' => now()->addDays(30)
                    ]);
                    $message = 'Selected items are now featured';
                    break;

                case 'remove_featured':
                    Menu::whereIn('id', $menuIds)->update([
                        'is_featured' => false,
                        'featured_until' => null
                    ]);
                    $message = 'Selected items removed from featured';
                    break;

                case 'delete':
                    // Delete associated images
                    foreach ($menus as $menu) {
                        if ($menu->images) {
                            foreach ($menu->images as $image) {
                                Storage::delete($image);
                            }
                        }
                    }
                    Menu::whereIn('id', $menuIds)->delete();
                    $message = 'Selected items deleted successfully';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action'
            ], 500);
        }
    }
    /**
     * Get validation rules for menu data
     */
    private function getValidationRules(Request $request): array // Requires $request
    {
        // Adjust validation for array fields now that they are structured inputs
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0.01|max:9999999.99',
            'discounted_price' => 'nullable|numeric|min:0.01|max:9999999.99|lt:price',
            'category' => 'required|string|in:appetizer,main,dessert,beverage,snack',
            'cuisine_types' => 'nullable|string|max:500', // Still processed as comma-separated string for simplicity
            'dietary_info' => 'nullable|string|max:500', // Still processed as comma-separated string for simplicity
            'preparation_time_minutes' => 'nullable|integer|min:1|max:1440',
            'serves_count' => 'required|integer|min:1|max:50',
            'ingredients' => 'nullable|string|max:2000',
            'allergens' => 'nullable|string|max:1000',
            'spice_level' => 'nullable|integer|min:0|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'stock_quantity' => 'nullable|integer|min:0|max:99999',
            'featured_until' => 'nullable|date|after:today',

            // NEW RULES FOR STRUCTURED INPUTS
            'nutritional_info_key.*' => 'nullable|string|max:100',
            'nutritional_info_value.*' => 'nullable|string|max:100',
            'availability_schedule' => 'nullable|array',
            'availability_schedule.*.available' => 'nullable|boolean',
            'availability_schedule.*.start_time' => 'nullable|date_format:H:i',
            'availability_schedule.*.end_time' => 'nullable|date_format:H:i|after:availability_schedule.*.start_time',
        ];

        return $rules;
    }
    /**
     * Validate menu data using DRY principle
     */
    private function validateMenuData(Request $request): array
    {
        return $request->validate($this->getValidationRules($request));
    }

    /**
     * Process and transform menu data
     */
    private function processMenuData(array $validated, Request $request, Menu $existingMenu = null): array
    {
        // 1. Handle image uploads (UNCHANGED)
        $validated['images'] = $this->handleImageUploads($request, $existingMenu);

        // 2. Process comma-separated fields into arrays (UNCHANGED)
        $validated = $this->processArrayFields($validated);

        // 3. Process special fields (REVISED)
        $validated = $this->processSpecialFields($validated, $request); // Pass $request for new inputs

        // 4. Handle checkboxes (UNCHANGED)
        $validated['is_available'] = $request->has('is_available');
        $validated['is_featured'] = $request->has('is_featured');

        // Remove transient inputs before final update
        unset($validated['nutritional_info_key'], $validated['nutritional_info_value']);

        return $validated;
    }

    /**
     * Handle image uploads with DRY principle
     */
    private function handleImageUploads(Request $request, Menu $existingMenu = null): array
    {
        if (!$request->hasFile('images')) {
            return $existingMenu ? $existingMenu->images ?? [] : [];
        }

        // Delete old images if updating
        if ($existingMenu && $existingMenu->images) {
            $this->deleteMenuImages($existingMenu);
        }

        $uploadedImages = [];
        foreach ($request->file('images') as $index => $image) {
            if ($index >= 5) break; // Limit to 5 images

            $filename = 'menu_' . time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('menu-images', $filename, 'public');
            $uploadedImages[] = $path;
        }

        return $uploadedImages;
    }

    /**
     * Process comma-separated fields into arrays
     */
    private function processArrayFields(array $validated): array
    {
        $arrayFields = ['cuisine_types', 'dietary_info', 'ingredients', 'allergens'];

        foreach ($arrayFields as $field) {
            if (isset($validated[$field]) && $validated[$field]) {
                $validated[$field] = array_filter(array_map('trim', explode(',', $validated[$field])));
            } else {
                $validated[$field] = [];
            }
        }

        return $validated;
    }

    /**
     * Process special fields (nutritional_info, availability_schedule)
     * REVISED to handle structured array inputs.
     */
    private function processSpecialFields(array $validated, Request $request): array
    {
        // 1. Process nutritional_info (from key/value arrays)
        $nutritionalInfo = [];
        $keys = $request->input('nutritional_info_key', []);
        $values = $request->input('nutritional_info_value', []);

        // Combine keys and values, filtering out empty entries
        foreach ($keys as $index => $key) {
            $value = $values[$index] ?? null;
            if (trim($key) && trim($value)) {
                $nutritionalInfo[trim($key)] = trim($value);
            }
        }
        $validated['nutritional_info'] = $nutritionalInfo;

        // 2. Process availability_schedule (from structured day arrays)
        $schedule = [];
        $rawSchedule = $request->input('availability_schedule', []);

        foreach ($rawSchedule as $day => $data) {
            $isAvailable = $data['available'] ?? false;

            if ($isAvailable) {
                // Only save the schedule if the day is checked as available
                $schedule[$day] = [
                    'available' => true,
                    'start_time' => $data['start_time'] ?? '00:00',
                    'end_time' => $data['end_time'] ?? '23:59',
                ];
            } else {
                $schedule[$day] = ['available' => false];
            }
        }
        $validated['availability_schedule'] = $schedule;

        return $validated;
    }

    /**
     * Process key:value formatted fields
     */
    private function processKeyValueField(string $field): array
    {
        if (empty($field)) {
            return [];
        }

        $result = [];
        $pairs = explode(',', $field);

        foreach ($pairs as $pair) {
            if (strpos($pair, ':') !== false) {
                [$key, $value] = explode(':', $pair, 2);
                $result[trim($key)] = trim($value);
            }
        }

        return $result;
    }

    /**
     * Generate unique slug for menu item
     */
    private function generateSlug(string $name): string
    {
        return Str::slug($name . '-' . Str::random(6));
    }

    /**
     * Get default tracking fields for new menu items
     */
    private function getDefaultTrackingFields(): array
    {
        return [
            'view_count' => 0,
            'order_count' => 0,
            'average_rating' => 0.00,
            'total_reviews' => 0,
        ];
    }

    /**
     * Delete menu images from storage
     */
    private function deleteMenuImages(Menu $menu): void
    {
        if ($menu->images && is_array($menu->images)) {
            foreach ($menu->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
    }
}