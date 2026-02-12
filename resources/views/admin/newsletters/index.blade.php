@extends('layouts.dashboard')

@section('title', 'Newsletter Subscribers')
@section('page_title', 'Newsletter Subscribers')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Subscribers ({{ $subscribers->total() }})</h3>
            <a href="{{ route('admin.newsletters.export') }}" class="px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>
        </div>

        @if(session('success'))
            <div class="p-4">
                <div class="bg-green-50 text-green-700 px-4 py-2 rounded-lg text-sm font-bold border border-green-200">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Subscribed At</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscribers as $subscriber)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-mono font-medium">{{ $subscriber->id }}</td>
                            <td class="px-6 py-4">{{ $subscriber->email }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $subscriber->created_at->toDayDateTimeString() }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.newsletters.delete', $subscriber->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this subscriber?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">No subscribers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $subscribers->links() }}
        </div>
    </div>
</div>
@endsection
