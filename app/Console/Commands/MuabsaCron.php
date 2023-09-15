<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use StoreProduct;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MuabsaCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'muabsa:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $datas = StoreProduct::all(['id', 'qty_available']);
            foreach ($datas as $data) {
                DB::table('store_product_balances')->insert([
                    'date' => date('Y-m-d'),
                    'qty' => $data->qty_available,
                    'store_product_id' => $data->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        return 0;
    }
}
