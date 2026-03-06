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
            'APP_URL' => $request->app_url,
            'DB_HOST' => $request->db_host,
            'DB_PORT' => $request->db_port,
            'DB_DATABASE' => $request->db_name,
            'DB_USERNAME' => $request->db_user,
            'DB_PASSWORD' => $request->db_pass ?? '',
        ];

        foreach ($updates as $key => $value) {
            $envContent = preg_replace("/^{$key}=(.*)$/m", "{$key}={$value}", $envContent);
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
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');

            // Set the installed flag
            File::put(storage_path('installed'), 'installed on ' . now()->toDateTimeString());

            return response()->json(['success' => true, 'message' => 'Installation completed successfully!']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Finalization failed: ' . $e->getMessage()], 500);
        }
    }
}
