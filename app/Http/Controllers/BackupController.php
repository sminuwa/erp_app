<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        $backupDir = storage_path('app/backups');
        $files = [];

        if (file_exists($backupDir)) {
            $files = glob($backupDir . '/*.sql');
        }

        return view('pages.backup_restore.list', compact('files'));
    }



    public function createBackup()
    {
        try {
            ini_set('memory_limit', '2G');
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            // Generate file name
            $filename = 'backup_' . date('Y-m-d-His') . '.sql';
            $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;

            // Get all table names
            $tables = DB::select('SHOW TABLES');
            $tables = array_map('current', json_decode(json_encode($tables), true));

            $sql = "-- Database Backup for " . env('DB_DATABASE') . " \n-- Generated: " . now() . "\n\n";

            foreach ($tables as $table) {
                $sql .= "-- Dumping data for table: $table \n";
                $rows = DB::select("SELECT * FROM $table");

                foreach ($rows as $row) {
                    $values = array_map(fn($value) => $value === null ? 'NULL' : "'" . addslashes($value) . "'", (array) $row);
                    $sql .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
                }
                $sql .= "\n";
            }

            File::put($backupPath, $sql);

            Log::info('Backup created successfully', ['path' => $backupPath]);

            return response()->json([
                'status' => 'success',
                'message' => 'Backup completed successfully',
                'reload' => true  // <-- Add a reload flag
            ]);
            
        } catch (\Exception $e) {
            Log::error('Backup failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Backup failed: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Handle Backup Failure
     */
    private function handleBackupFailure($e)
    {
        if (request()->ajax()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }

        return redirect()->route('backup.index')->with('error', 'Backup failed: ' . $e->getMessage());
    }


    public function downloadBackup($file)
    {
        try {
            $backupPath = storage_path("app/backups/{$file}");

            if (!file_exists($backupPath)) {
                return redirect()->route('backup.index')->with('error', 'Backup file not found');
            }

            return response()->download($backupPath);
        } catch (\Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    public function restoreDatabase(Request $request)
    {
        try {
            $file = $request->file('backup_file');

            if (!$file) {
                throw new \Exception('No backup file selected.');
            }

            // Verify file extension
            if ($file->getClientOriginalExtension() !== 'sql') {
                throw new \Exception('Invalid file type. Only SQL files are allowed.');
            }

            // Store the uploaded file
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $filePath = $tempDir . DIRECTORY_SEPARATOR . $file->getClientOriginalName();
            $file->move($tempDir, $file->getClientOriginalName());

            // Database credentials
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            // MySQL restore command
            $command = sprintf(
                '"C:\\wamp64\\bin\\mysql\\mysql5.7.36\\bin\\mysql" -h %s -u %s -p%s %s < %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            exec($command, $output, $returnVar);

            // Clean up
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            if ($returnVar !== 0) {
                throw new \Exception('Database restore failed');
            }

            return redirect()->route('backup.index')->with('success', 'Database restored successfully!');
        } catch (\Exception $e) {
            Log::error('Restore failed', [
                'error' => $e->getMessage()
            ]);
            return redirect()->route('backup.index')->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }
    public function deleteBackup($file)
    {
        $filePath = storage_path('app/backups/' . $file);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('backup.index')->with('success', 'Backup deleted successfully.');
        }

        return redirect()->route('backup.index')->with('error', 'File not found.');
    }
}