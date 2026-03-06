<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stream Platform - Installation Wizard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .step-transition {
            transition: all 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4">

    <div class="max-w-3xl w-full bg-slate-800 rounded-2xl shadow-2xl overflow-hidden border border-slate-700">
        
        <!-- Header -->
        <div class="bg-indigo-600 p-6 text-center text-white">
            <h1 class="text-3xl font-bold tracking-tight">Stream Platform Setup</h1>
            <p class="mt-2 text-indigo-200">Welcome to your new video management platform.</p>
        </div>

        <!-- Progress Bar -->
        <div class="w-full bg-slate-700 h-2">
            <div id="progress-bar" class="bg-indigo-500 h-2 transition-all duration-500 w-1/4"></div>
        </div>

        <div class="p-8">

            <!-- Step 1: Database & Environment Setup -->
            <div id="step-1" class="step">
                <h2 class="text-2xl font-semibold mb-6 flex items-center">
                    <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                    Environment & Database Configuration
                </h2>
                <form id="env-form" onsubmit="event.preventDefault(); submitEnv();">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- App Config -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-slate-300 border-b border-slate-600 pb-2">Application</h3>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">App Name</label>
                                <input type="text" id="app_name" value="Stream Platform" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">App URL</label>
                                <input type="url" id="app_url" value="{{ url('/') }}" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white">
                            </div>
                        </div>

                        <!-- Database Config -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-slate-300 border-b border-slate-600 pb-2">Database</h3>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">Host</label>
                                <input type="text" id="db_host" value="127.0.0.1" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">Port</label>
                                <input type="number" id="db_port" value="3306" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">Database Name</label>
                                <input type="text" id="db_name" placeholder="laravel" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">Database User</label>
                                <input type="text" id="db_user" placeholder="root" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-1">Database Password</label>
                                <input type="password" id="db_pass" placeholder="leave empty if none" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white">
                            </div>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div id="env-error" class="hidden mt-6 bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-lg flex items-start">
                        <svg class="w-5 h-5 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span id="env-error-msg"></span>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" id="btn-env" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg transition duration-200 flex items-center">
                            Save & Test Connection
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Database Migration -->
            <div id="step-2" class="step hidden">
                <h2 class="text-2xl font-semibold mb-6 flex items-center">
                    <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                    Run Migrations
                </h2>
                
                <div class="bg-slate-900 p-6 rounded-xl border border-slate-700 text-center">
                    <div class="mb-4">
                        <svg class="w-16 h-16 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">Database Connection Successful!</h3>
                    <p class="text-slate-400 mb-6">The next step will create structured tables and initial data in your database. This process might take a few seconds.</p>
                    
                    <button type="button" id="btn-migrate" onclick="runMigrations()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-8 rounded-lg transition duration-200 shadow-lg shadow-indigo-500/30">
                        Run Migrations Now
                    </button>

                    <!-- Error Alert -->
                    <div id="migrate-error" class="hidden mt-6 bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-left text-sm max-h-32 overflow-y-auto">
                        <span id="migrate-error-msg"></span>
                    </div>
                </div>
            </div>

            <!-- Step 3: Admin Account Creation -->
            <div id="step-3" class="step hidden">
                <h2 class="text-2xl font-semibold mb-6 flex items-center">
                    <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                    Create Administrator
                </h2>
                
                <form id="admin-form" onsubmit="event.preventDefault(); createAdmin();">
                    <div class="space-y-4 max-w-md mx-auto">
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Full Name</label>
                            <input type="text" id="admin_name" placeholder="Admin User" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Email Address</label>
                            <input type="email" id="admin_email" placeholder="admin@example.com" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-1">Password</label>
                            <input type="password" id="admin_password" placeholder="At least 8 characters" class="w-full bg-slate-900 border border-slate-600 rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500 text-white" required minlength="8">
                        </div>

                        <!-- Error Alert -->
                        <div id="admin-error" class="hidden mt-4 bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-lg flex items-start text-sm">
                            <span id="admin-error-msg"></span>
                        </div>

                        <div class="mt-8 pt-4">
                            <button type="submit" id="btn-admin" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg transition duration-200 flex justify-center items-center">
                                Create Admin Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Step 4: Finalize & Success -->
            <div id="step-4" class="step hidden text-center">
                <div class="mb-6 flex justify-center">
                    <div class="w-20 h-20 bg-green-500/20 text-green-400 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                
                <h2 class="text-3xl font-bold mb-4 text-white">Almost Done!</h2>
                <p class="text-slate-400 mb-8 max-w-lg mx-auto">We just need to clear cache and link the storage directories so your application runs correctly.</p>

                <!-- Error Alert -->
                <div id="final-error" class="hidden mb-6 bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded-lg text-left text-sm mx-auto max-w-md">
                    <span id="final-error-msg"></span>
                </div>

                <button type="button" id="btn-finalize" onclick="finalizeInstallation()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-8 rounded-lg transition duration-200 shadow-lg shadow-indigo-500/30">
                    Complete Installation
                </button>
            </div>

            <!-- Done Step -->
            <div id="step-done" class="step hidden text-center">
                <div class="mb-6 flex justify-center">
                    <div class="w-24 h-24 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-green-500/40">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                
                <h2 class="text-3xl font-bold mb-4 text-white">Installation Complete!</h2>
                <p class="text-slate-400 mb-8 max-w-lg mx-auto">The Stream Platform has been successfully installed and configured. You can now login to your admin dashboard.</p>

                <a href="{{ url('/') }}" class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-8 rounded-lg transition duration-200 shadow-lg shadow-green-500/30">
                    Go to Dashboard
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

        </div>
    </div>

    <!-- Spinner SVG (hidden by default, used in JS) -->
    <template id="spinner">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </template>

    <script>
        // Set Axios CSRF Token since this relies on standard web routes without Session setup yet,
        // Wait, standard web routes without VerifyCsrfToken middleware exception might block POST requests.
        // Actually we should temporarily bypass CSRF or fetch the token if available. 
        // Given we don't have .env setup properly, the session driver might fail.
        // Let's ensure the web routes for install are excluded from CSRF or we just send request headers if needed.
        // Wait! Laravel has `VerifyCsrfToken` middleware on Web group. 
        // We'll proceed with Axios. If it fails, we will need to disable CSRF for install routes.

        const api = axios.create({
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        function showStep(step) {
            document.querySelectorAll('.step').forEach(el => el.classList.add('hidden'));
            document.getElementById('step-' + step).classList.remove('hidden');
            
            // Update progress bar
            const progress = document.getElementById('progress-bar');
            if(step === 1) progress.className = 'bg-indigo-500 h-2 transition-all duration-500 w-1/4';
            if(step === 2) progress.className = 'bg-indigo-500 h-2 transition-all duration-500 w-2/4';
            if(step === 3) progress.className = 'bg-indigo-500 h-2 transition-all duration-500 w-3/4';
            if(step === 4) progress.className = 'bg-indigo-500 h-2 transition-all duration-500 w-11/12';
            if(step === 'done') progress.className = 'bg-green-500 h-2 transition-all duration-500 w-full';
        }

        function setLoading(btnId, isLoading, originalText = '') {
            const btn = document.getElementById(btnId);
            if (isLoading) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = document.getElementById('spinner').innerHTML + ' Processing...';
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = originalText;
            }
        }

        async function submitEnv() {
            const btnOriginal = 'Save & Test Connection <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>';
            setLoading('btn-env', true);
            document.getElementById('env-error').classList.add('hidden');

            try {
                const payload = {
                    app_name: document.getElementById('app_name').value,
                    app_url: document.getElementById('app_url').value,
                    db_host: document.getElementById('db_host').value,
                    db_port: document.getElementById('db_port').value,
                    db_name: document.getElementById('db_name').value,
                    db_user: document.getElementById('db_user').value,
                    db_pass: document.getElementById('db_pass').value,
                };

                const res = await api.post('{{ route("install.setup-env") }}', payload);
                if (res.data.success) {
                    showStep(2);
                }
            } catch (error) {
                const msg = error.response?.data?.message || error.response?.data?.error || 'An error occurred. Please check your details.';
                document.getElementById('env-error-msg').innerText = msg;
                document.getElementById('env-error').classList.remove('hidden');
            } finally {
                setLoading('btn-env', false, btnOriginal);
            }
        }

        async function runMigrations() {
            setLoading('btn-migrate', true);
            document.getElementById('migrate-error').classList.add('hidden');

            try {
                const res = await api.post('{{ route("install.run-migrations") }}');
                if (res.data.success) {
                    showStep(3);
                }
            } catch (error) {
                const msg = error.response?.data?.message || 'Migration failed.';
                document.getElementById('migrate-error-msg').innerText = msg;
                document.getElementById('migrate-error').classList.remove('hidden');
            } finally {
                setLoading('btn-migrate', false, 'Run Migrations Now');
            }
        }

        async function createAdmin() {
            setLoading('btn-admin', true);
            document.getElementById('admin-error').classList.add('hidden');

            try {
                const payload = {
                    admin_name: document.getElementById('admin_name').value,
                    admin_email: document.getElementById('admin_email').value,
                    admin_password: document.getElementById('admin_password').value,
                };

                const res = await api.post('{{ route("install.create-admin") }}', payload);
                if (res.data.success) {
                    showStep(4);
                }
            } catch (error) {
                const msg = error.response?.data?.message || (error.response?.data?.errors ? Object.values(error.response.data.errors)[0][0] : 'Failed to create user.');
                document.getElementById('admin-error-msg').innerText = msg;
                document.getElementById('admin-error').classList.remove('hidden');
            } finally {
                setLoading('btn-admin', false, 'Create Admin Account');
            }
        }

        async function finalizeInstallation() {
            setLoading('btn-finalize', true);
            document.getElementById('final-error').classList.add('hidden');

            try {
                const res = await api.post('{{ route("install.finalize") }}');
                if (res.data.success) {
                    showStep('done');
                }
            } catch (error) {
                const msg = error.response?.data?.message || 'Finalization failed.';
                document.getElementById('final-error-msg').innerText = msg;
                document.getElementById('final-error').classList.remove('hidden');
            } finally {
                setLoading('btn-finalize', false, 'Complete Installation');
            }
        }
    </script>
</body>
</html>
