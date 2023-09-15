<?php

namespace App\Providers;

use App\Models\ProductExpireSetting;
use App\Models\PurchaseProduct;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $date = Carbon::today()->subDays(30);
        //$expires = PurchaseProduct::where('expire_date', '>=', $date)->get();
        $no_of_days = ProductExpireSetting::where('no_of_days', '>', 0)->first()?->no_of_days;
        $expires = PurchaseProduct::where(\DB::raw("TO_DAYS(expire_date)-TO_DAYS(NOW())"), "<=", 180)
            ->where('expire_date', '>=', Carbon::now())
            ->get();
        //view()->share('new_request', PurchaseProduct::where('expire_date', '<=', Carbon::now())->count());
        view()->share('expires', $expires);
    }
}