<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // <-- Required!
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PerformDatabaseBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        try {
            ini_set('memory_limit', '2G');
            $backupDir = storage_path('app/backups');
            File::ensureDirectoryExists($backupDir);

            $filename = 'backup_' . date('Y-m-d-His') . '.sql';
            $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;
            $handle = fopen($backupPath, 'w');

            fwrite($handle, "-- Backup created at " . now() . "\n\n");

            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $tableName = current((array) $table);
                fwrite($handle, "-- Table: $tableName\n");
                $rows = DB::table($tableName)->cursor();

                foreach ($rows as $row) {
                    $values = array_map(fn($v) => is_null($v) ? 'NULL' : "'" . addslashes($v) . "'", (array) $row);
                    fwrite($handle, "INSERT INTO `$tableName` VALUES (" . implode(', ', $values) . ");\n");
                }
            }

            fclose($handle);
            Log::info("✅ Backup completed: $filename");
        } catch (\Throwable $e) {
            Log::error('❌ Backup failed: ' . $e->getMessage());
        }
    }
}
