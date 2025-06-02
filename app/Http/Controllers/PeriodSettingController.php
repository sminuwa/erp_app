<?php

namespace App\Http\Controllers;

use App\Models\PeriodSetting;
use Illuminate\Http\Request;

class PeriodSettingController extends Controller
{
    public function index()
    {
        $period = PeriodSetting::first();
        return view('pages.users.period_setting', compact('period'));
    }

    public function update(Request $request)
    {
        if ($request->has('reset_range')) {
            PeriodSetting::updateOrCreate(
                ['id' => 1],
                ['period_open' => null, 'period_close' => null]
            );
        } else {
            $request->validate([
                'period_open' => 'required|date',
                'period_close' => 'required|date|after_or_equal:period_open',
            ]);

            PeriodSetting::updateOrCreate(
                ['id' => 1],
                [
                    'period_open' => $request->period_open,
                    'period_close' => $request->period_close,
                ]
            );
        }

        return back()->with('success', 'Global period setting updated.');
    }
}
