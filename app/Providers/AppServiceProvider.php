<?php

namespace App\Providers;

use App\Models\ProductExpireSetting;
use App\Models\PurchaseProduct;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        // Set default string length for older MySQL versions
        Schema::defaultStringLength(191);

        // Product expiration logic
        $date = Carbon::today()->subDays(30);
        $no_of_days = ProductExpireSetting::where('no_of_days', '>', 0)->first()?->no_of_days;

        $expires = PurchaseProduct::where(\DB::raw("TO_DAYS(expire_date)-TO_DAYS(NOW())"), "<=", 180)
            ->where('expire_date', '>=', Carbon::now())
            ->get();

        View::share('expires', $expires);

        // Share user-specific date range for datepicker
        View::composer('*', function ($view) {
            $user = Auth::user();

            $dateRangeStart = null;
            $dateRangeEnd = null;

            // Priority 1: User-specific period
            if ($user && $user->date_range_start && $user->date_range_end) {
                $dateRangeStart = $user->date_range_start;
                $dateRangeEnd = $user->date_range_end;
            } else {
                // Priority 2: Global period
                $period = \App\Models\PeriodSetting::first();
                if ($period && $period->period_open && $period->period_close) {
                    $dateRangeStart = $period->period_open;
                    $dateRangeEnd = $period->period_close;
                }
            }

            $view->with('dateRangeStart', $dateRangeStart);
            $view->with('dateRangeEnd', $dateRangeEnd);
        });
    }
}