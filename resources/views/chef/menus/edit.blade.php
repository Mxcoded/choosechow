@extends('layouts.dashboard')

@section('title', 'Edit Dish')

@section('content')

<div class="max-w-3xl mx-auto pt-6">
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <h3 class="font-bold text-lg mb-2">⚠️ Update Failed</h3>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

<div class="max-w-3xl mx-auto pb-20">
    
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-4 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Edit Dish ✏️</h1>
            <p class="text-gray-600 dark:text-gray-400">Update details for {{ $menu->name }}</p>
        </div>
        <a href="{{ route('chef.menus.index') }}" class="tdark:text-gray-300 hover:dark:text-gray-300 font-medium transition-colors">
            <i class="fas fa-times mr-1"></i> Cancel
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form action="{{ route('chef.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Dish Name <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" required>
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Price (₦) <span class="text-red-600">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $menu->price) }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 font-bold dark:text-gray-300" min="0" required>
                </div>

                {{-- Prep Time (Added to match Controller) --}}
                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Prep Time (Mins)</label>
                    <input type="number" name="preparation_time" value="{{ old('preparation_time', $menu->preparation_time) }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" min="0" placeholder="e.g. 15">
                </div>

                {{-- Category (Updated List) --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Category <span class="text-red-600">*</span></label>
                    @php
                        $categories = ['Main Dish', 'Side Dish', 'Soup/Swallow', 'Grills', 'Snacks', 'Drinks', 'Combo', 'Rice Dishes', 'Swallow & Soups', 'Pasta', 'Grills & BBQ', 'Pastries', 'Breakfast', 'Continental', 'Vegetarian', 'Drinks & Smoothies', 'Fast Food', 'Local Delicacies', 'Seafood'];
                        $currentCategory = old('category', $menu->category);
                        // Ensure current category is in the list
                        if ($currentCategory && !in_array($currentCategory, $categories)) {
                            array_unshift($categories, $currentCategory);
                        }
                    @endphp
                    <select name="category" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 bg-white" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $currentCategory == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3" 
                          class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" placeholder="Describe the ingredients and taste...">{{ old('description', $menu->description) }}</textarea>
            </div>

            {{-- Availability Toggle (Added to match Controller) --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <span class="block text-sm font-bold text-gray-800">Available for Order?</span>
                    <span class="text-xs tdark:text-gray-300">Uncheck to mark as "Sold Out"</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" class="sr-only peer" {{ $menu->is_available ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                </label>
            </div>

            {{-- Image Upload --}}
            <div>
                <label class="block text-sm font-bold dark:text-gray-300 mb-2">Food Image</label>
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        {{-- Logic: 1. Storage Image, 2. Default Plate, 3. Placeholder --}}
                        <img id="imgPreview" 
                             src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/default-plate.png') }}" 
                             class="h-32 w-32 object-cover rounded-lg border-2 border-dashed border-gray-300 bg-gray-50"
                             onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" onchange="previewFile(this)" accept="image/*"
                               class="block w-full text-sm text-slate-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-full file:border-0
                               file:text-sm file:font-semibold
                               file:bg-red-50 file:text-red-700
                               hover:file:bg-red-100 cursor-pointer"/>
                        <p class="text-xs tdark:text-gray-300 mt-2">Leave empty to keep current image. Max 2MB.</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                {{-- Delete (Optional shortcut) --}}
                <button type="button" onclick="if(confirm('Delete this dish permanently?')) document.getElementById('delete-form-{{ $menu->id }}').submit();" class="text-red-500 text-sm font-bold hover:underline">
                    Delete Dish
                </button>

                <button type="submit" class="bg-red-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-red-700 shadow-lg transform transition hover:-translate-y-0.5">
                    Update Dish
                </button>
            </div>
        </form>
    </div>

    {{-- Hidden Delete Form for the button above --}}
    <form id="delete-form-{{ $menu->id }}" action="{{ route('chef.menus.destroy', $menu->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>

<script>
    function previewFile(input) {
        var file = input.files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                document.getElementById('imgPreview').src = reader.result;
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection