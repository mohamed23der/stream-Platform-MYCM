<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SecureStream') — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full z-30">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-xl font-bold flex items-center gap-2">
                    <i class="fas fa-shield-alt text-indigo-400"></i>
                    SecureStream
                </h1>
                <p class="text-gray-400 text-xs mt-1">Video Hosting Platform</p>
            </div>

            <nav class="flex-1 py-4 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-gray-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-indigo-400 border-r-2 border-indigo-400' : 'text-gray-300' }}">
                    <i class="fas fa-tachometer-alt w-5"></i> Dashboard
                </a>
                <a href="{{ route('admin.videos.index') }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-gray-800 transition {{ request()->routeIs('admin.videos.*') ? 'bg-gray-800 text-indigo-400 border-r-2 border-indigo-400' : 'text-gray-300' }}">
                    <i class="fas fa-video w-5"></i> Videos
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-gray-800 transition {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-indigo-400 border-r-2 border-indigo-400' : 'text-gray-300' }}">
                    <i class="fas fa-users w-5"></i> Users
                </a>
                <a href="{{ route('admin.domains.index') }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-gray-800 transition {{ request()->routeIs('admin.domains.*') ? 'bg-gray-800 text-indigo-400 border-r-2 border-indigo-400' : 'text-gray-300' }}">
                    <i class="fas fa-globe w-5"></i> Allowed Domains
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-gray-800 transition {{ request()->routeIs('admin.analytics.*') ? 'bg-gray-800 text-indigo-400 border-r-2 border-indigo-400' : 'text-gray-300' }}">
                    <i class="fas fa-chart-bar w-5"></i> Access Logs
                </a>
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-gray-800 transition {{ request()->routeIs('admin.settings.*') ? 'bg-gray-800 text-indigo-400 border-r-2 border-indigo-400' : 'text-gray-300' }}">
                    <i class="fas fa-cog w-5"></i> Settings
                </a>
            </nav>

            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-sm font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="w-full text-left text-sm text-gray-400 hover:text-white transition flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64">
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-20">
                <h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                <div class="flex items-center gap-4">
                    @yield('header-actions')
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mx-8 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-8 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                    <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if($errors->any())
                <div class="mx-8 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Content -->
            <div class="p-8">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
