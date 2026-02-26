<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoJob;
use App\Models\Video;
use App\Services\StreamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    public function __construct(protected StreamService $streamService) {}

    public function index()
    {
        $videos = Video::with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['storage_driver'] = config('securestream.storage_driver', 'local');

        $video = Video::create($validated);

        return response()->json([
            'success' => true,
            'video_id' => $video->id,
            'upload_url' => route('admin.videos.upload-chunk', $video),
        ]);
    }

    public function uploadChunk(Request $request, Video $video)
    {
        $request->validate([
            'chunk' => 'required|file',
            'chunk_index' => 'required|integer|min:0',
            'total_chunks' => 'required|integer|min:1',
        ]);

        $chunkDir = storage_path('app/chunks/' . $video->id);
        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }

        $chunkIndex = $request->input('chunk_index');
        $totalChunks = $request->input('total_chunks');

        $request->file('chunk')->move($chunkDir, "chunk_{$chunkIndex}");

        $uploadedChunks = count(glob($chunkDir . '/chunk_*'));

        if ($uploadedChunks >= $totalChunks) {
            $finalPath = storage_path('app/uploads/' . $video->id . '/' . Str::uuid() . '.mp4');
            $finalDir = dirname($finalPath);
            if (!is_dir($finalDir)) {
                mkdir($finalDir, 0755, true);
            }

            $outputFile = fopen($finalPath, 'wb');
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $chunkDir . "/chunk_{$i}";
                if (file_exists($chunkPath)) {
                    $chunkStream = fopen($chunkPath, 'rb');
                    stream_copy_to_stream($chunkStream, $outputFile);
                    fclose($chunkStream);
                    unlink($chunkPath);
                }
            }
            fclose($outputFile);
            rmdir($chunkDir);

            $video->update(['file_path' => $finalPath]);

            ProcessVideoJob::dispatch($video->id, $finalPath);

            return response()->json([
                'success' => true,
                'message' => 'Upload complete. Processing started.',
                'status' => 'processing',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Chunk {$chunkIndex} uploaded.",
            'uploaded' => $uploadedChunks,
            'total' => $totalChunks,
        ]);
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|in:public,private',
        ]);

        $video->update($validated);

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video updated successfully.');
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return redirect()->route('admin.videos.index')
            ->with('success', 'Video deleted successfully.');
    }

    public function show(Video $video)
    {
        $video->load(['accessLogs', 'allowedDomains']);
        $videoHash = $this->streamService->generateVideoHash($video->id);
        $embedCode = $this->generateEmbedCode($video);
        $shareLink = route('watch.show', ['hash' => $videoHash]);

        return view('admin.videos.show', compact('video', 'embedCode', 'videoHash', 'shareLink'));
    }

    public function status(Video $video)
    {
        return response()->json([
            'status' => $video->status,
            'duration' => $video->duration,
            'resolution' => $video->resolution,
        ]);
    }

    public function addDomain(Request $request, Video $video)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $video->allowedDomains()->firstOrCreate([
            'domain' => $request->input('domain'),
        ]);

        return redirect()->route('admin.videos.show', $video)
            ->with('success', 'Domain added successfully.');
    }

    public function removeDomain(Video $video, $domainId)
    {
        $video->allowedDomains()->where('id', $domainId)->delete();

        return redirect()->route('admin.videos.show', $video)
            ->with('success', 'Domain removed successfully.');
    }

    protected function generateEmbedCode(Video $video): string
    {
        $hash = $this->streamService->generateVideoHash($video->id);
        $url = route('embed.show', ['hash' => $hash]);

        return '<iframe src="' . $url . '" width="800" height="450" frameborder="0" allowfullscreen></iframe>';
    }
}
