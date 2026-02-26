<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Models\VideoAccessLog;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_videos' => Video::count(),
            'videos_processing' => Video::where('status', 'processing')->count(),
            'videos_ready' => Video::where('status', 'ready')->count(),
            'videos_failed' => Video::where('status', 'failed')->count(),
            'total_access' => VideoAccessLog::count(),
            'blocked_access' => VideoAccessLog::where('blocked', true)->count(),
        ];

        $recentVideos = Video::with('creator')
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = VideoAccessLog::with('video')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentVideos', 'recentLogs'));
    }
}
