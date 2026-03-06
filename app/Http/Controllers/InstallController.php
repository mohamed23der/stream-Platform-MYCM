<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Exception;

class InstallController extends Controller
{
    /**
     * Show the installation wizard index page.
     */
    public function index()
    {
        return view('install.index');
    }

    /**
     * Step 1: Save basic app and database configuration.
     */
    public function setupEnv(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string',
            'app_url' => 'required|url',
            'db_host' => 'required|string',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_pass' => 'nullable|string',
            'db_port' => 'required|numeric',
        ]);

        $envFile = base_path('.env');
        if (!File::exists($envFile)) {
            if (File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), $envFile);
            } else {
                return response()->json(['error' => '.env.example file not found.'], 500);
            }
        }

        $envContent = File::get($envFile);

        $updates = [
            'APP_NAME' => '"' . $request->app_name . '"',
            'APP_URL' => rtrim($request->app_url, '/'),
            'DB_HOST' => $request->db_host,
            'DB_PORT' => $request->db_port,
            'DB_DATABASE' => $request->db_name,
            'DB_USERNAME' => $request->db_user,
            'DB_PASSWORD' => $request->db_pass ?? '',
        ];

        // Ensure session variables match the given APP_URL to prevent 419 CSRF issues
        $parsedUrl = parse_url($updates['APP_URL']);
        if (isset($parsedUrl['host'])) {
            $updates['SESSION_DOMAIN'] = $parsedUrl['host'];
        }
        $updates['SESSION_PATH'] = isset($parsedUrl['path']) && $parsedUrl['path'] !== '' ? $parsedUrl['path'] : '/';

        foreach ($updates as $key => $value) {
            // First check if the key exists to either replace it or append it
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=(.*)$/m", "{$key}={$value}", $envContent);
            } else {
                $envContent .= "\n{$key}={$value}\n";
            }
        }

        File::put($envFile, $envContent);

        Artisan::call('config:clear');

        // Test Connection
        try {
            DB::connection()->getPdo();
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Could not connect to the database. Please check your configuration. Error: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'message' => 'Environment saved and connection successful!']);
    }

    /**
     * Step 2: Run Migrations.
     */
    public function runMigrations()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            return response()->json(['success' => true, 'message' => 'Database migrated successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Migration failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Step 3: Create Admin User.
     */
    public function createAdmin(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'admin_password' => 'required|min:8',
        ]);

        try {
            // Check if user exists
            $userExists = DB::table('users')->where('email', $request->admin_email)->first();

            if ($userExists) {
                return response()->json(['success' => false, 'message' => 'An administrator with this email already exists.'], 400);
            }

            DB::table('users')->insert([
                'id' => (string) str()->uuid(),
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin', // Adjust based on your actual roles schema
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Admin user created successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create admin user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Step 4: Finalize Installation.
     */
    public function finalize()
    {
        try {
            // Put system in final production state
            Artisan::call('storage:link');
            Artisan::call('key:generate', ['--force' => true]);
            
            // Clear all caches securely rather than caching stale requests
            Artisan::call('optimize:clear');

            // Set the installed flag
            File::put(storage_path('installed'), 'installed on ' . now()->toDateTimeString());

            return response()->json(['success' => true, 'message' => 'Installation completed successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Finalization failed: ' . $e->getMessage()], 500);
        }
    }
}
