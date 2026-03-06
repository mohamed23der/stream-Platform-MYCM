<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\File;

class CheckIfInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the application is already installed, let the request proceed.
        if (File::exists(storage_path('installed'))) {
            return $next($request);
        }

        // If not installed and trying to access anything other than /install, redirect to /install.
        if (! $request->is('install*')) {
            return redirect()->route('install.index');
        }

        return $next($request);
    }
}
