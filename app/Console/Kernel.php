<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\StoreProduct;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $datas = StoreProduct::all(['id', 'qty_available']);
            foreach ($datas as $data) {
                DB::table('store_product_balances')->updateOrInsert(['date'=>date('Y-m-d'),'store_product_id' => $data->id],[
                    'date' => date('Y-m-d'),
                    'qty' => $data->qty_available,
                    'store_product_id' => $data->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        })->daily();
        $schedule->command('auth:clear-resets')->daily();
        $schedule->command('database:backup')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
