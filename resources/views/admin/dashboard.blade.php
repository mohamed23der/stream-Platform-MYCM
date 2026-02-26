@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-video text-green-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_videos'] }}</p>
                <p class="text-sm text-gray-500">Total Videos</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-eye text-indigo-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['total_access'] }}</p>
                <p class="text-sm text-gray-500">Total Access</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-ban text-red-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['blocked_access'] }}</p>
                <p class="text-sm text-gray-500">Blocked Attempts</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-amber-600 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['videos_ready'] }}</p>
                <p class="text-sm text-gray-500">Videos Ready</p>
            </div>
        </div>
    </div>
</div>

<!-- Processing Status -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span class="text-sm text-gray-600">Ready</span>
            <span class="ml-auto font-bold text-gray-800">{{ $stats['videos_ready'] }}</span>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
            <span class="text-sm text-gray-600">Processing</span>
            <span class="ml-auto font-bold text-gray-800">{{ $stats['videos_processing'] }}</span>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3">
            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
            <span class="text-sm text-gray-600">Failed</span>
            <span class="ml-auto font-bold text-gray-800">{{ $stats['videos_failed'] }}</span>
        </div>
    </div>
</div>

<!-- Recent Videos & Access Logs -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Recent Videos</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentVideos as $video)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-film text-gray-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $video->title }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($video->visibility) }} &middot; {{ $video->storage_driver }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $video->status === 'ready' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $video->status === 'processing' ? 'bg-yellow-100 text-yellow-700' : '' }}
                        {{ $video->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $video->status === 'pending' ? 'bg-gray-100 text-gray-700' : '' }}
                    ">{{ ucfirst($video->status) }}</span>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-400">
                    <i class="fas fa-video-slash text-2xl mb-2"></i>
                    <p class="text-sm">No videos yet</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-800">Recent Access Logs</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentLogs as $log)
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 {{ $log->blocked ? 'bg-red-100' : 'bg-indigo-100' }} rounded-full flex items-center justify-center">
                        <i class="fas {{ $log->blocked ? 'fa-ban text-red-500' : 'fa-play text-indigo-500' }} text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800">
                            <span class="font-medium">{{ $log->ip_address }}</span>
                            {{ $log->blocked ? 'blocked from' : 'accessed' }}
                            <span class="font-medium">{{ $log->video->title ?? 'Unknown' }}</span>
                        </p>
                        <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                    @if($log->blocked)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700 font-medium">Blocked</span>
                    @endif
                </div>
            @empty
                <div class="px-6 py-8 text-center text-gray-400">
                    <i class="fas fa-chart-line text-2xl mb-2"></i>
                    <p class="text-sm">No access logs yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
