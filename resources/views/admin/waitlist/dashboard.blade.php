@extends('layouts.dashboard')

@section('title', 'Waitlist Analytics')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-content-primary">Waitlist Analytics</h1>
            <p class="text-gray-600 dark:text-content-secondary mt-1">Pre-launch demand and supply insights</p>
        </div>
        <a href="{{ route('admin.waitlist.export') }}" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-accent text-white font-bold rounded-lg hover:bg-accent-hover transition">
            <i class="fas fa-download mr-2"></i> Export CSV
        </a>
    </div>

    {{-- Overview Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-dark-card rounded-xl p-6 border border-gray-200 dark:border-dark-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-content-secondary">Total Signups</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-content-primary">{{ number_format($totalSignups) }}</p>
                </div>
                <div class="w-12 h-12 bg-accent/10 dark:bg-accent/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-accent text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-xl p-6 border border-gray-200 dark:border-dark-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-content-secondary">Food Lovers</p>
                    <p class="text-3xl font-bold text-accent-light">{{ number_format($foodLovers) }}</p>
                </div>
                <div class="w-12 h-12 bg-accent-light/10 rounded-full flex items-center justify-center">
                    <i class="fas fa-utensils text-accent-light text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-xl p-6 border border-gray-200 dark:border-dark-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-content-secondary">Vendors</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($vendors) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-store text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-xl p-6 border border-gray-200 dark:border-dark-border">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-content-secondary">Demand:Supply</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $demandSupplyRatio }}:1</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-balance-scale text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gray-50 dark:bg-dark-section rounded-lg p-4">
            <p class="text-xs text-gray-500 dark:text-content-secondary uppercase tracking-wide">Survey Rate</p>
            <p class="text-xl font-bold text-gray-900 dark:text-content-primary">{{ $surveyCompletionRate }}%</p>
        </div>
        <div class="bg-gray-50 dark:bg-dark-section rounded-lg p-4">
            <p class="text-xs text-gray-500 dark:text-content-secondary uppercase tracking-wide">Via Referrals</p>
            <p class="text-xl font-bold text-gray-900 dark:text-content-primary">{{ number_format($totalReferrals) }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-dark-section rounded-lg p-4">
            <p class="text-xs text-gray-500 dark:text-content-secondary uppercase tracking-wide">Viral Coefficient</p>
            <p class="text-xl font-bold text-gray-900 dark:text-content-primary">{{ $viralCoefficient }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-dark-section rounded-lg p-4">
            <p class="text-xs text-gray-500 dark:text-content-secondary uppercase tracking-wide">Surveys Done</p>
            <p class="text-xl font-bold text-gray-900 dark:text-content-primary">{{ number_format($surveysCompleted) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        {{-- Top Neighborhoods --}}
        <div class="bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-dark-border">
                <h2 class="font-bold text-gray-900 dark:text-content-primary">
                    <i class="fas fa-map-marker-alt text-accent mr-2"></i> Top Neighborhoods
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-section">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-content-secondary uppercase">Area</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-content-secondary uppercase">Total</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-content-secondary uppercase">Demand</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-content-secondary uppercase">Supply</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-dark-border">
                        @forelse($signupsByNeighborhood as $area)
                            <tr class="hover:bg-gray-50 dark:hover:bg-dark-section">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-content-primary">{{ $area['name'] }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-600 dark:text-content-secondary">{{ $area['total'] }}</td>
                                <td class="px-4 py-3 text-sm text-center text-accent-light">{{ $area['food_lovers'] }}</td>
                                <td class="px-4 py-3 text-sm text-center text-green-600">{{ $area['vendors'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400">No data yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Vendor Categories --}}
        <div class="bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-dark-border">
                <h2 class="font-bold text-gray-900 dark:text-content-primary">
                    <i class="fas fa-store text-green-600 mr-2"></i> Vendor Types
                </h2>
            </div>
            <div class="p-4 space-y-3">
                @forelse($vendorsByCategory as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-500/20 rounded-full flex items-center justify-center mr-3">
                                <i class="fas {{ $category['icon'] }} text-green-600 text-sm"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-content-primary">{{ $category['name'] }}</span>
                        </div>
                        <span class="text-sm font-bold text-gray-600 dark:text-content-secondary">{{ $category['total'] }}</span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-4">No vendors yet</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        {{-- Traffic Sources --}}
        <div class="bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-dark-border">
                <h2 class="font-bold text-gray-900 dark:text-content-primary">
                    <i class="fas fa-chart-pie text-blue-600 mr-2"></i> Traffic Sources
                </h2>
            </div>
            <div class="p-4">
                <h3 class="text-xs font-bold text-gray-500 dark:text-content-secondary uppercase mb-3">UTM Sources</h3>
                @forelse($utmSources as $source)
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-700 dark:text-content-secondary">{{ $source->utm_source }}</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-content-primary">{{ $source->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No UTM tracking data</p>
                @endforelse

                <h3 class="text-xs font-bold text-gray-500 dark:text-content-secondary uppercase mt-6 mb-3">Manual Discovery</h3>
                @forelse($discoverySources as $source)
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-700 dark:text-content-secondary">{{ ucfirst(str_replace('_', ' ', $source->discovery_source)) }}</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-content-primary">{{ $source->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No discovery source data</p>
                @endforelse
            </div>
        </div>

        {{-- Top Referrers --}}
        <div class="bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-dark-border">
                <h2 class="font-bold text-gray-900 dark:text-content-primary">
                    <i class="fas fa-user-friends text-purple-600 mr-2"></i> Top Referrers
                </h2>
            </div>
            <div class="p-4 space-y-3">
                @forelse($topReferrers as $index => $referrer)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="w-6 h-6 bg-purple-100 dark:bg-purple-500/20 rounded-full flex items-center justify-center text-xs font-bold text-purple-600 mr-3">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-content-primary">{{ $referrer->name }}</span>
                                <p class="text-xs text-gray-400">{{ $referrer->email }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-purple-600">{{ $referrer->referrals_count }} refs</span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-4">No referrals yet</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Popular Meals & Recent Signups --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Popular Meals --}}
        <div class="bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-dark-border">
                <h2 class="font-bold text-gray-900 dark:text-content-primary">
                    <i class="fas fa-fire text-orange-500 mr-2"></i> Most Wanted Meals
                </h2>
            </div>
            <div class="p-4">
                @forelse($popularMeals as $meal => $count)
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-700 dark:text-content-secondary capitalize">{{ $meal }}</span>
                        <span class="px-2 py-1 bg-orange-100 dark:bg-orange-500/20 text-orange-600 text-xs font-bold rounded-full">{{ $count }}</span>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-4">No meal data yet</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Signups --}}
        <div class="bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-dark-border">
                <h2 class="font-bold text-gray-900 dark:text-content-primary">
                    <i class="fas fa-clock text-gray-400 mr-2"></i> Recent Signups
                </h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-dark-border">
                @forelse($recentSignups as $signup)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-content-primary">{{ $signup->name }}</p>
                            <p class="text-xs text-gray-400">{{ $signup->neighborhood?->name }} · {{ $signup->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-bold rounded-full {{ $signup->role === 'vendor' ? 'bg-green-100 dark:bg-green-500/20 text-green-600' : 'bg-accent-light/20 text-accent-light' }}">
                            {{ $signup->role_display }}
                        </span>
                    </div>
                @empty
                    <p class="p-4 text-center text-gray-400">No signups yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
