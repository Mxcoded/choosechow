@extends('layouts.dashboard')

@section('title', 'Edit Store Profile')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Store Settings 🏪</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your business profile, hours, and banking details.</p>
    </div>

    {{-- Error Handling --}}
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border-2 border-red-500 rounded-lg p-4 mb-6">
            <h4 class="font-bold text-red-700 mb-2">Please fix the following errors:</h4>
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('chef.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- LEFT COLUMN: Avatar & Status --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 text-center">
                    <div class="relative inline-block group">
                        <img id="avatarPreview" 
                             src="{{ $chef->avatar ? asset('storage/' . $chef->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($chef->full_name).'&background=fca5a5&color=7f1d1d' }}" 
                             class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-lg mx-auto">
                        
                        <label for="avatar" class="absolute bottom-2 right-2 bg-gray-900 text-white p-2.5 rounded-full cursor-pointer hover:bg-red-600 transition-colors shadow-md">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <p class="text-xs tdark:text-gray-300 mt-3 font-medium">Click icon to upload (Max 5MB)</p>
                </div>

                {{-- Status Card --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <label class="flex items-center justify-between cursor-pointer group">
                        <span class="font-bold text-gray-800">Accepting Orders?</span>
                        <div class="relative">
                            <input type="checkbox" name="is_online" value="1" class="sr-only peer" {{ old('is_online', $profile->is_online) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                        </div>
                    </label>
                    <p class="text-xs tdark:text-gray-300 mt-2">Toggle this off if you are closed or busy.</p>
                </div>
            </div>

            {{-- RIGHT COLUMN: Main Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    {{-- Tabs --}}
                    <div class="flex border-b border-gray-100 dark:border-gray-700 overflow-x-auto">
                        <button type="button" onclick="switchTab('info')" id="tab-info" class="px-6 py-4 text-sm font-bold text-red-600 border-b-2 border-red-600 hover:bg-gray-50 transition-colors">
                            General Info
                        </button>
                        <button type="button" onclick="switchTab('hours')" id="tab-hours" class="px-6 py-4 text-sm font-bold tdark:text-gray-300 hover:dark:text-gray-300 hover:bg-gray-50 transition-colors">
                            Operating Hours
                        </button>
                        <button type="button" onclick="switchTab('bank')" id="tab-bank" class="px-6 py-4 text-sm font-bold tdark:text-gray-300 hover:dark:text-gray-300 hover:bg-gray-50 transition-colors">
                            Bank Details
                        </button>
                    </div>

                    <div class="p-6">
                        
                        {{-- TAB 1: General Info --}}
                        <div id="content-info" class="space-y-6">
                            
                            {{-- Personal Details --}}
                            <div>
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">Personal Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold dark:text-gray-300 mb-1">First Name</label>
                                        <input type="text" name="first_name" value="{{ old('first_name', $chef->first_name) }}" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-700 focus:border-red-500 focus:ring-0 p-2.5 font-medium" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold dark:text-gray-300 mb-1">Last Name</label>
                                        <input type="text" name="last_name" value="{{ old('last_name', $chef->last_name) }}" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-700 focus:border-red-500 focus:ring-0 p-2.5 font-medium" required>
                                    </div>
                                    
                                    {{-- PHONE FIELD (FORCED VISIBILITY) --}}
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-extrabold text-gray-900 dark:text-gray-100 mb-1">
                                            Phone Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="phone" 
                                               value="{{ old('phone', $chef->phone) }}" 
                                               class="w-full rounded-lg border-2 border-black bg-red-50/50 focus:border-red-600 focus:ring-0 p-3 font-bold text-gray-900 dark:text-gray-100" 
                                               placeholder="08012345678" required>
                                        <p class="text-xs tdark:text-gray-300 mt-1">Required for order alerts.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Business Details --}}
                            <div>
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 dark:border-gray-700 pb-2 pt-4">Business Details</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold dark:text-gray-300 mb-1">Kitchen / Business Name</label>
                                        <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name) }}" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-700 focus:border-red-500 focus:ring-0 p-2.5" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold dark:text-gray-300 mb-1">Bio / Story</label>
                                        <textarea name="bio" rows="3" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-700 focus:border-red-500 focus:ring-0 p-2.5" required>{{ old('bio', $profile->bio) }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold dark:text-gray-300 mb-1">Kitchen Address</label>
                                        <input type="text" name="kitchen_address" value="{{ old('kitchen_address', $profile->kitchen_address) }}" class="w-full rounded-lg border-2 border-gray-200 dark:border-gray-700 focus:border-red-500 focus:ring-0 p-2.5" placeholder="Full Street Address" required>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Years of Experience (Black Border) --}}
                                        <div>
                                            <label class="block text-sm font-extrabold text-gray-900 dark:text-gray-100 mb-1">Years of Experience</label>
                                            <input type="number" name="years_of_experience" 
                                                   value="{{ old('years_of_experience', $profile->years_of_experience) }}" 
                                                   class="w-full rounded-lg border-2 border-black focus:border-red-600 focus:ring-0 p-2.5 font-bold" min="0" required>
                                        </div>
                                        {{-- Minimum Order (Black Border) --}}
                                        <div>
                                            <label class="block text-sm font-extrabold text-gray-900 dark:text-gray-100 mb-1">Min. Order (₦)</label>
                                            <input type="number" name="minimum_order" 
                                                   value="{{ old('minimum_order', $profile->minimum_order) }}" 
                                                   class="w-full rounded-lg border-2 border-black focus:border-red-600 focus:ring-0 p-2.5 font-bold" min="0" step="100" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Cuisines --}}
                            <div class="pt-4">
                                <label class="block text-sm font-bold dark:text-gray-300 mb-3">Cuisines & Specialties</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 bg-gray-50 rounded-xl border border-gray-200 dark:border-gray-700">
                                    @foreach($cuisines as $cuisine)
                                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                            <input type="checkbox" name="cuisine_ids[]" value="{{ $cuisine->id }}" 
                                                   class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                                   {{ in_array($cuisine->id, old('cuisine_ids', $profile->cuisines->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <span class="text-sm font-medium dark:text-gray-300">{{ $cuisine->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('cuisine_ids') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- TAB 2: Operating Hours --}}
                        <div id="content-hours" class="hidden space-y-4">
                            @include('chefs.profile.partials.operating_hours')
                        </div>

                        {{-- TAB 3: Bank Details --}}
                        <div id="content-bank" class="hidden space-y-4">
                            <div class="bg-blue-50 text-blue-800 p-4 rounded-lg text-sm mb-4">
                                <i class="fas fa-lock mr-2"></i> Your banking details are encrypted and secure.
                            </div>
                            <div>
                                <label class="block text-sm font-bold dark:text-gray-300 mb-1">Bank Name</label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $profile->bank_name) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 p-2.5">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Account Number</label>
                                    <input type="text" name="account_number" value="{{ old('account_number', $profile->account_number) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 p-2.5">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold dark:text-gray-300 mb-1">Account Name</label>
                                    <input type="text" name="account_name" value="{{ old('account_name', $profile->account_name) }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 p-2.5">
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    {{-- Footer Save Button --}}
                    <div class="bg-gray-50 p-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit" class="bg-gray-900 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:bg-black transform transition hover:-translate-y-0.5">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Simple Tab Switcher Script
    function switchTab(tabName) {
        // Hide all contents
        ['info', 'hours', 'bank'].forEach(name => {
            document.getElementById('content-' + name).classList.add('hidden');
            const btn = document.getElementById('tab-' + name);
            btn.classList.remove('text-red-600', 'border-b-2', 'border-red-600');
            btn.classList.add('tdark:text-gray-300');
        });

        // Show active content
        document.getElementById('content-' + tabName).classList.remove('hidden');
        const activeBtn = document.getElementById('tab-' + tabName);
        activeBtn.classList.remove('tdark:text-gray-300');
        activeBtn.classList.add('text-red-600', 'border-b-2', 'border-red-600');
    }

    // Avatar Preview
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection