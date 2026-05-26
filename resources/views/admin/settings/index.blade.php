@extends('layouts.dashboard')

@section('title', 'System Settings')
@section('page_title', 'Platform Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        {{-- General Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-cog text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">General Settings</h3>
                    <p class="text-xs text-gray-500">Basic platform configuration</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Platform Name</label>
                        <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'ChooseChow' }}" 
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                        <p class="text-xs text-gray-400 mt-1">The name displayed throughout the platform</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tagline</label>
                        <input type="text" name="tagline" value="{{ $settings['tagline'] ?? 'Delicious Food, Delivered' }}" 
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Currency Symbol</label>
                        <input type="text" name="currency" value="{{ $settings['currency'] ?? '₦' }}" 
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Financial Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-coins text-green-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Financial Settings</h3>
                    <p class="text-xs text-gray-500">Commission and payout configuration</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Platform Commission (%)</label>
                        <div class="relative">
                            <input type="number" name="commission_fee" value="{{ $settings['commission_fee'] ?? '5' }}" 
                                min="0" max="100" step="0.1"
                                class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500 pr-8">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-400">%</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Fee taken from each order</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Min. Withdrawal (₦)</label>
                        <input type="number" name="min_withdrawal" value="{{ $settings['min_withdrawal'] ?? '5000' }}" 
                            min="0"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                        <p class="text-xs text-gray-400 mt-1">Minimum payout amount for chefs</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Min. Order Amount (₦)</label>
                        <input type="number" name="min_order" value="{{ $settings['min_order'] ?? '500' }}" 
                            min="0"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                        <p class="text-xs text-gray-400 mt-1">Minimum order value for customers</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery Settings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                <div class="bg-orange-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-motorcycle text-orange-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Delivery Settings</h3>
                    <p class="text-xs text-gray-500">Delivery fees and radius configuration</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Base Delivery Fee (₦)</label>
                        <input type="number" name="delivery_fee_base" value="{{ $settings['delivery_fee_base'] ?? '500' }}" 
                            min="0"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Fee Per KM (₦)</label>
                        <input type="number" name="delivery_fee_per_km" value="{{ $settings['delivery_fee_per_km'] ?? '100' }}" 
                            min="0"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Max Delivery Radius (KM)</label>
                        <input type="number" name="max_delivery_radius" value="{{ $settings['max_delivery_radius'] ?? '15' }}" 
                            min="1"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Contact & Support --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-headset text-purple-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Contact & Support</h3>
                    <p class="text-xs text-gray-500">Customer support contact information</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Support Email</label>
                        <input type="email" name="support_email" value="{{ $settings['support_email'] ?? '' }}" 
                            placeholder="support@choosechow.com"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Support Phone</label>
                        <input type="text" name="support_phone" value="{{ $settings['support_phone'] ?? '' }}" 
                            placeholder="+234 xxx xxx xxxx"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">WhatsApp Number</label>
                        <input type="text" name="whatsapp" value="{{ $settings['whatsapp'] ?? '' }}" 
                            placeholder="+234 xxx xxx xxxx"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Business Address</label>
                        <input type="text" name="business_address" value="{{ $settings['business_address'] ?? '' }}" 
                            placeholder="Lagos, Nigeria"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Social Media --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                <div class="bg-pink-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-share-alt text-pink-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Social Media</h3>
                    <p class="text-xs text-gray-500">Social media links</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fab fa-instagram text-pink-500 mr-1"></i> Instagram
                        </label>
                        <input type="url" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}" 
                            placeholder="https://instagram.com/choosechow"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fab fa-twitter text-blue-400 mr-1"></i> Twitter/X
                        </label>
                        <input type="url" name="social_twitter" value="{{ $settings['social_twitter'] ?? '' }}" 
                            placeholder="https://twitter.com/choosechow"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook
                        </label>
                        <input type="url" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}" 
                            placeholder="https://facebook.com/choosechow"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fab fa-tiktok text-black mr-1"></i> TikTok
                        </label>
                        <input type="url" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}" 
                            placeholder="https://tiktok.com/@choosechow"
                            class="w-full rounded-lg border-gray-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Platform Controls --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center">
                <div class="bg-red-100 p-2 rounded-lg mr-3">
                    <i class="fas fa-sliders-h text-red-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Platform Controls</h3>
                    <p class="text-xs text-gray-500">Enable or disable platform features</p>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-bold text-gray-700">New User Registration</p>
                            <p class="text-xs text-gray-400">Allow new customers to sign up</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="allow_registration" value="0">
                            <input type="checkbox" name="allow_registration" value="1" 
                                {{ ($settings['allow_registration'] ?? '1') == '1' ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-bold text-gray-700">New Chef Registration</p>
                            <p class="text-xs text-gray-400">Allow new vendors to register</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="allow_chef_registration" value="0">
                            <input type="checkbox" name="allow_chef_registration" value="1" 
                                {{ ($settings['allow_chef_registration'] ?? '1') == '1' ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-bold text-gray-700">Ordering Enabled</p>
                            <p class="text-xs text-gray-400">Allow customers to place orders</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="ordering_enabled" value="0">
                            <input type="checkbox" name="ordering_enabled" value="1" 
                                {{ ($settings['ordering_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-bold text-gray-700">Maintenance Mode</p>
                            <p class="text-xs text-gray-400">Show maintenance page to visitors</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" name="maintenance_mode" value="1" 
                                {{ ($settings['maintenance_mode'] ?? '0') == '1' ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end">
            <button type="submit" class="bg-gray-900 text-white font-bold py-3 px-8 rounded-lg hover:bg-gray-800 transition shadow-lg inline-flex items-center">
                <i class="fas fa-save mr-2"></i> Save All Settings
            </button>
        </div>
    </form>
</div>
@endsection
