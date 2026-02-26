@extends('layouts.admin')

@section('title', $video->title)
@section('page-title', $video->title)

@section('header-actions')
    <div class="flex gap-2">
        <a href="{{ route('admin.videos.edit', $video) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
            <i class="fas fa-edit mr-1"></i> Edit
        </a>
        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" onsubmit="return confirm('Delete this video?')">
            @csrf @method('DELETE')
            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition">
                <i class="fas fa-trash mr-1"></i> Delete
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Video Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Video Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Video Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Status</p>
                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full font-medium
                        {{ $video->status === 'ready' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $video->status === 'processing' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $video->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $video->status === 'pending' ? 'bg-gray-100 text-gray-700' : '' }}
                    ">{{ ucfirst($video->status) }}</span>
                </div>
                <div>
                    <p class="text-gray-500">Visibility</p>
                    <p class="font-medium text-gray-800 mt-1">{{ ucfirst($video->visibility) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Duration</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $video->duration ? gmdate('H:i:s', $video->duration) : '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Resolution</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $video->resolution ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Storage</p>
                    <p class="font-medium text-gray-800 mt-1">{{ ucfirst($video->storage_driver) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Created</p>
                    <p class="font-medium text-gray-800 mt-1">{{ $video->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            @if($video->description)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-gray-500 text-sm mb-1">Description</p>
                    <p class="text-gray-700 text-sm">{{ $video->description }}</p>
                </div>
            @endif
        </div>

        <!-- Embed & Share -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="{ tab: 'embed' }">
            <h3 class="font-semibold text-gray-800 mb-4">Share & Embed</h3>

            <div class="flex gap-2 mb-4">
                <button @click="tab = 'embed'" :class="tab === 'embed' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                    Embed Code
                </button>
                <button @click="tab = 'link'" :class="tab === 'link' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600'" class="px-3 py-1.5 rounded-lg text-sm font-medium transition">
                    Direct Link
                </button>
            </div>

            <div x-show="tab === 'embed'">
                <div class="relative">
                    <textarea id="embed-code" readonly rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs font-mono text-gray-700">{{ $embedCode }}</textarea>
                    <button onclick="navigator.clipboard.writeText(document.getElementById('embed-code').value); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000)"
                        class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                        Copy
                    </button>
                </div>
            </div>

            <div x-show="tab === 'link'">
                <div class="relative">
                    <input id="share-link" type="text" readonly value="{{ $shareLink }}" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 text-sm font-mono text-gray-700 pr-20">
                    <button onclick="navigator.clipboard.writeText(document.getElementById('share-link').value); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000)"
                        class="absolute top-2 right-2 bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded text-xs font-medium transition">
                        Copy
                    </button>
                </div>
            </div>
        </div>

        <!-- Access Logs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Recent Access Logs</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">IP</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Referer</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($video->accessLogs->take(20) as $log)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700 font-mono">{{ $log->ip_address }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500 truncate max-w-xs">{{ $log->referer ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    @if($log->blocked)
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Blocked</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Allowed</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-sm">No access logs yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Domain Management -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Allowed Domains</h3>
            <p class="text-xs text-gray-500 mb-4">Only these domains can embed or access this video. If empty, global domains apply.</p>

            <form action="{{ route('admin.videos.domains.store', $video) }}" method="POST" class="flex gap-2 mb-4">
                @csrf
                <input type="text" name="domain" placeholder="example.com" required
                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    Add
                </button>
            </form>

            <div class="space-y-2">
                @forelse($video->allowedDomains as $domain)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2">
                        <span class="text-sm text-gray-700 font-mono">{{ $domain->domain }}</span>
                        <form action="{{ route('admin.videos.domains.destroy', [$video, $domain->id]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 transition text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No specific domains. Global domains will be used.</p>
                @endforelse
            </div>
        </div>

        <!-- Video Preview -->
        @if($video->status === 'ready')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Preview</h3>
            <div class="aspect-video bg-black rounded-lg overflow-hidden">
                <iframe src="{{ route('embed.show', ['hash' => $videoHash]) }}" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
