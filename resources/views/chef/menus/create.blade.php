@extends('layouts.dashboard')

@section('title', 'Add New Dish')

@section('content')

<div class="max-w-3xl mx-auto pt-6">
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            <h3 class="font-bold text-lg mb-2">⚠️ Submission Failed</h3>
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
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Add New Dish 🍳</h1>
            <p class="text-gray-600 dark:text-gray-400">Add a delicious item to your menu.</p>
        </div>
        <a href="{{ route('chef.menus.index') }}" class="tdark:text-gray-300 hover:dark:text-gray-300 font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Back to Menu
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form action="{{ route('chef.menus.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Dish Name <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" 
                           placeholder="e.g. Jollof Rice & Chicken" required>
                </div>

                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Price (₦) <span class="text-red-600">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" 
                           class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 font-bold dark:text-gray-300" 
                           placeholder="2500" min="0" required>
                </div>

                <div>
                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Category <span class="text-red-600">*</span></label>
                    <select name="category" class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 bg-white">
                        <option value="Main Dish">Main Dish</option>
                        <option value="Side Dish">Side Dish</option>
                        <option value="Soup/Swallow">Soup / Swallow</option>
                        <option value="Grills">Grills & Suya</option>
                        <option value="Snacks">Snacks / Pastries</option>
                        <option value="Drinks">Drinks</option>
                        <option value="Combo">Combo Meal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3" 
                          class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500" 
                          placeholder="Describe the ingredients and taste...">{{ old('description') }}</textarea>
                <p class="text-xs tdark:text-gray-300 mt-1">Keep it short and appetizing (Max 500 chars).</p>
            </div>

            <div>
                <label class="block text-sm font-bold dark:text-gray-300 mb-2">Food Image</label>
                <div class="flex items-center space-x-6">
                    <div class="shrink-0">
                        <img id="imgPreview" src="https://via.placeholder.com/150?text=Preview" 
                             class="h-32 w-32 object-cover rounded-lg border-2 border-dashed border-gray-300 bg-gray-50">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="image" onchange="previewFile(this)" accept="image/*"
                               class="block w-full text-sm text-slate-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-full file:border-0
                               file:text-sm file:font-semibold
                               file:bg-red-50 file:text-red-700
                               hover:file:bg-red-100 cursor-pointer"/>
                        <p class="text-xs tdark:text-gray-300 mt-2">Recommended: Square image, less than 2MB.</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                <button type="submit" class="bg-red-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-red-700 shadow-lg transform transition hover:-translate-y-0.5">
                    Save Dish
                </button>
            </div>
        </form>
    </div>
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