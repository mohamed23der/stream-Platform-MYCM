<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Failed — My Communication Stream</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-600 rounded-2xl mb-4">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">My Communication Stream</h1>
            <p class="text-gray-400 text-sm mt-1">Video Hosting Platform</p>
        </div>

        <div class="bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-700 text-center">
            <h2 class="text-xl font-semibold text-white mb-4">Login Failed</h2>

            <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-4 rounded-lg mb-6">
                <p>The provided credentials do not match our records. Please try again with the correct email and password.</p>
            </div>

            <a href="{{ route('login') }}" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to Login
            </a>
        </div>

        <p class="text-center text-gray-500 text-xs mt-6">
            &copy; {{ date('Y') }} My Communication Stream. All rights reserved.
        </p>
    </div>
</body>
</html>
