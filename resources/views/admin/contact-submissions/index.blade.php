@extends('layouts.dashboard')

@section('title', 'Contact Submissions')
@section('page_title', 'Contact Submissions')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Messages ({{ $submissions->total() }})</h3>
            <span class="text-sm text-gray-500">Latest messages first</span>
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
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">From</th>
                        <th class="px-6 py-3">Subject</th>
                        <th class="px-6 py-3">Received</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($submissions as $submission)
                        <tr class="hover:bg-gray-50 {{ $submission->status === 'new' ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-bold
                                    {{ $submission->status === 'new' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $submission->status === 'read' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $submission->status === 'resolved' ? 'bg-green-100 text-green-700' : '' }}
                                ">
                                    {{ ucfirst($submission->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold">{{ $submission->name }}</div>
                                <div class="text-xs text-gray-500">{{ $submission->email }}</div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $submission->subject }}</td>
                            <td class="px-6 py-4 text-gray-600 text-xs">{{ $submission->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 flex gap-2">
                                @if($submission->status === 'new')
                                    <form action="{{ route('admin.contact-submissions.read', $submission->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700">Mark Read</button>
                                    </form>
                                @endif
                                @if($submission->status !== 'resolved')
                                    <form action="{{ route('admin.contact-submissions.resolve', $submission->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Resolve</button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.contact-submissions.delete', $submission->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this message?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="5" class="px-6 py-3">
                                <div class="text-sm text-gray-700 border-l-4 border-gray-300 pl-4 italic">
                                    {{ $submission->message }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No contact submissions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $submissions->links() }}
        </div>
    </div>
</div>
@endsection
