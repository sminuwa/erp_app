<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\PerformDatabaseBackup;
use App\Jobs\RestoreDatabaseFromBackup;
use Illuminate\Support\Facades\Storage;
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


    public function deleteBackup($file)
    {
        $filePath = storage_path('app/backups/' . $file);

        if (File::exists($filePath)) {
            File::delete($filePath);
            return redirect()->route('backup.index')->with('success', 'Backup deleted successfully.');
        }

        return redirect()->route('backup.index')->with('error', 'File not found.');
    }
    public function startBackup(Request $request)
    {
        PerformDatabaseBackup::dispatch();

        return $request->ajax()
            ? response()->json(['status' => 'success', 'message' => 'Backup started. Please wait...'])
            : back()->with('success', 'Backup has been queued.');
    }

    public function startRestore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql',
        ]);

        $path = $request->file('backup_file')->storeAs('backups', $request->file('backup_file')->getClientOriginalName());

        RestoreDatabaseFromBackup::dispatch(storage_path('app/' . $path));

        return back()->with('success', 'Restore job queued. Check logs for completion.');
    }

}