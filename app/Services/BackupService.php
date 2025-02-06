<?php

namespace App\Services;

use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class BackupService
{
    protected function findMysqldumpPath()
    {
        // Common WAMP MySQL version directories
        $wampBase = 'C:\\wamp64\\bin\\mysql';
        $possiblePaths = [];

        // Check if directory exists
        if (is_dir($wampBase)) {
            // Get all MySQL version directories
            $directories = glob($wampBase . '/*', GLOB_ONLYDIR);

            foreach ($directories as $dir) {
                $mysqldumpPath = $dir . '\\bin\\mysqldump.exe';
                if (file_exists($mysqldumpPath)) {
                    $possiblePaths[] = $mysqldumpPath;
                }
            }
        }

        // Log found paths
        Log::info('Found MySQL dump paths:', ['paths' => $possiblePaths]);

        return !empty($possiblePaths) ? $possiblePaths[0] : null;
    }

    public function createBackup()
    {
        try {
            // Find mysqldump.exe
            $mysqldumpPath = $this->findMysqldumpPath();

            if (!$mysqldumpPath) {
                throw new \Exception('Could not find mysqldump.exe in WAMP directory');
            }

            // Get directory path only
            $dumpBinaryPath = dirname($mysqldumpPath);

            Log::info('Using MySQL dump binary:', [
                'full_path' => $mysqldumpPath,
                'binary_path' => $dumpBinaryPath
            ]);

            // Create temp directory with escaped backslashes
            $tempDir = str_replace('\\', '\\\\', storage_path('app\\backup-temp'));
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $dumpPath = $tempDir . '\\\\' . date('Y-m-d-His') . '-dump.sql';

            MySql::create()
                ->setHost(config('database.connections.mysql.host'))
                ->setDbName(config('database.connections.mysql.database'))
                ->setUserName(config('database.connections.mysql.username'))
                ->setPassword(config('database.connections.mysql.password'))
                ->setDumpBinaryPath($dumpBinaryPath)
                ->addExtraOption('--no-tablespaces')
                ->dumpToFile($dumpPath);

            return [
                'status' => 'success',
                'message' => 'Backup completed successfully',
                'path' => $dumpPath
            ];

        } catch (\Exception $e) {
            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}