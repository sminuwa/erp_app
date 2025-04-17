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

class RestoreDatabaseFromBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function handle()
    {
        try {
            $sql = File::get($this->path);
            DB::unprepared($sql);
            Log::info("✅ Database restored from backup: " . basename($this->path));
        } catch (\Throwable $e) {
            Log::error('❌ Restore failed: ' . $e->getMessage());
        }
    }
}
