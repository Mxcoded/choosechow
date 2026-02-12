@extends('layouts.dashboard')

@section('title', 'Analytics & Reports')
@section('page_title', 'Business Intelligence')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-20">

    {{-- 1. HEADER & DATE FILTER --}}
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Performance Overview</h2>
            <p class="text-sm tdark:text-gray-300"> analyzing data from <span class="font-mono font-bold">{{ $startDate }}</span> to <span class="font-mono font-bold">{{ $endDate }}</span></p>
        </div>
        
        <form action="{{ route('admin.reports') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div>
                <label class="block text-xs font-bold tdark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-red-500 focus:border-red-500">
            </div>
            <div>
                <label class="block text-xs font-bold tdark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-red-500 focus:border-red-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-gray-800 transition shadow-md h-[38px]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- 2. KEY METRICS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Revenue --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Gross Revenue</p>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mt-1">₦{{ number_format($totalRevenue) }}</h3>
            </div>
            <i class="fas fa-wallet absolute right-4 bottom-4 text-gray-100 text-5xl"></i>
        </div>

        {{-- Platform Profit --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-green-600 uppercase tracking-wider">Net Profit (5%)</p>
                <h3 class="text-2xl font-extrabold text-green-700 mt-1">₦{{ number_format($platformProfit) }}</h3>
            </div>
            <i class="fas fa-coins absolute right-4 bottom-4 text-green-50 text-5xl"></i>
        </div>

        {{-- Total Orders --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Total Orders</p>
                <h3 class="text-2xl font-extrabold text-blue-700 mt-1">{{ number_format($totalOrdersCount) }}</h3>
            </div>
            <i class="fas fa-shopping-bag absolute right-4 bottom-4 text-blue-50 text-5xl"></i>
        </div>

        {{-- Avg Order Value --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-xs font-bold text-purple-600 uppercase tracking-wider">Avg. Order Value</p>
                <h3 class="text-2xl font-extrabold text-purple-700 mt-1">₦{{ number_format($averageOrderValue) }}</h3>
            </div>
            <i class="fas fa-chart-line absolute right-4 bottom-4 text-purple-50 text-5xl"></i>
        </div>
    </div>

    {{-- 3. CHARTS SECTION --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Main Chart: Sales Trend --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Sales Trend</h3>
            <div class="relative h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Side List: Top Dishes --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Top Selling Dishes 🍔</h3>
            <div class="space-y-4">
                @forelse($topDishes as $index => $dish)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-lg font-bold text-gray-300 w-6">#{{ $index + 1 }}</span>
                            <span class="font-medium dark:text-gray-300 ml-2 truncate max-w-[120px]" title="{{ $dish->menu_name }}">
                                {{ $dish->menu_name }}
                            </span>
                        </div>
                        <span class="bg-red-50 text-red-600 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $dish->total_qty }} sold
                        </span>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-10">No dishes sold in this period.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 4. DATA TABLES --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50">
            <h3 class="font-bold text-gray-800">Top Performing Kitchens</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white tdark:text-gray-300 font-bold uppercase text-xs border-b">
                    <tr>
                        <th class="px-6 py-3">Kitchen Name</th>
                        <th class="px-6 py-3">Location</th>
                        <th class="px-6 py-3 text-right">Revenue Generated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topChefs as $chef)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                {{ $chef->chefProfile->business_name ?? $chef->first_name }}
                            </td>
                            <td class="px-6 py-4 tdark:text-gray-300">
                                {{ $chef->chefProfile->city ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                ₦{{ number_format($chef->chef_orders_sum_total_amount ?? 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-8 text-center tdark:text-gray-300">No data found for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- 5. CHART.JS SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    // Prepare Data from PHP
    const labels = {!! json_encode($salesChartData->pluck('date')) !!};
    const data = {!! json_encode($salesChartData->pluck('total')) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Sales (₦)',
                data: data,
                borderColor: '#dc2626', // Red-600
                backgroundColor: 'rgba(220, 38, 38, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4 // Smooth curves
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [2, 4] }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection