@extends('layouts.dashboard')

@section('title', 'My Menu')

@section('content')
<div class="max-w-6xl mx-auto">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b border-gray-100 dark:border-gray-700 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">My Menu 🥘</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your dishes, prices, and availability.</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('chef.menus.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform transition hover:-translate-y-0.5 flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Dish
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($menus->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($menus as $menu)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow group">
                    
                    {{-- IMAGE CONTAINER --}}
                    <div class="relative h-48 bg-gray-100 overflow-hidden">
                        {{-- 
                            1. Check if DB has image
                            2. Use asset('storage/...')
                            3. Fallback to default-plate.png
                            4. Final fallback to online placeholder via onerror 
                        --}}
                        <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/default-plate.png') }}" 
                             alt="{{ $menu->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                        
                        {{-- Availability Badge --}}
                        <div class="absolute top-3 right-3">
                            @if($menu->is_available)
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full border border-green-200 shadow-sm backdrop-blur-sm bg-opacity-90">
                                    In Stock
                                </span>
                            @else
                                <span class="bg-gray-800 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                    Sold Out
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- CARD BODY --}}
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-gray-100 text-lg leading-tight">{{ $menu->name }}</h3>
                                <span class="text-xs font-medium tdark:text-gray-300 uppercase tracking-wide">{{ $menu->category }}</span>
                            </div>
                            <span class="font-bold text-red-600 text-lg">₦{{ number_format($menu->price) }}</span>
                        </div>
                        
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2 h-10">{{ $menu->description ?? 'No description provided.' }}</p>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                            
                            {{-- Toggle Form --}}
                            <form action="{{ route('chef.menus.toggle', $menu->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-sm font-medium transition-colors {{ $menu->is_available ? 'text-green-600 hover:text-green-800' : 'tdark:text-gray-300 hover:dark:text-gray-300' }}">
                                    <i class="fas fa-power-off mr-1"></i> {{ $menu->is_available ? 'Mark Sold Out' : 'Mark Available' }}
                                </button>
                            </form>

                            {{-- Actions --}}
                            <div class="flex space-x-3">
                                <a href="{{ route('chef.menus.edit', $menu->id) }}" class="text-gray-400 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('chef.menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Delete this dish?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700">
            <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto shadow-sm mb-6">
                <i class="fas fa-utensils text-3xl text-gray-300"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Your Menu is Empty</h3>
            <p class="tdark:text-gray-300 mb-8 max-w-md mx-auto">Start building your menu by adding your best dishes. Add photos and descriptions to attract customers.</p>
            <a href="{{ route('chef.menus.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full shadow-lg transition-all">
                Create First Dish
            </a>
        </div>
    @endif
</div>
@endsection