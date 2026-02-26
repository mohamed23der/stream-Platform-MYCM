@extends('layouts.admin')

@section('title', 'Videos')
@section('page-title', 'Videos')

@section('header-actions')
    <a href="{{ route('admin.videos.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
        <i class="fas fa-upload"></i> Upload Video
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Resolution</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Storage</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($videos as $video)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $video->title }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($video->visibility) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $video->duration ? gmdate('H:i:s', $video->duration) : '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $video->resolution ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                {{ $video->status === 'ready' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $video->status === 'processing' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $video->status === 'failed' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $video->status === 'pending' ? 'bg-gray-100 text-gray-700' : '' }}
                            ">{{ ucfirst($video->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <i class="fas {{ $video->storage_driver === 'google' ? 'fa-cloud text-blue-500' : 'fa-hdd text-gray-500' }}"></i>
                            {{ ucfirst($video->storage_driver) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.videos.show', $video) }}" class="text-gray-400 hover:text-indigo-600 transition" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.videos.edit', $video) }}" class="text-gray-400 hover:text-indigo-600 transition" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="inline" onsubmit="return confirm('Delete this video?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-video text-3xl mb-3"></i>
                            <p>No videos yet. Upload your first video.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $videos->links() }}
    </div>
</div>
@endsection
