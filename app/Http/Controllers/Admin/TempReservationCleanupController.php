<?php

// TEMPORARY — manufacturing reservation cleanup runner for shared hosting.
// REMOVE this file, the matching view, and the route entries after the
// one-off cleanup has been completed.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TempReservationCleanupController extends Controller
{
    public function index()
    {
        return view('pages.admin.temp_reservation_cleanup', [
            'output' => null,
            'mode' => null,
            'error' => null,
        ]);
    }

    public function run(Request $request)
    {
        $apply = $request->boolean('apply');
        $error = null;

        if ($apply && strtoupper(trim((string) $request->input('confirm'))) !== 'APPLY') {
            return view('pages.admin.temp_reservation_cleanup', [
                'output' => null,
                'mode' => null,
                'error' => 'Type APPLY in the confirmation box to run in apply mode.',
            ]);
        }

        $params = [];
        if ($apply) {
            $params['--apply'] = true;
        }

        Artisan::call('manufacturing:cleanup-stale-schedule-reservations', $params);
        $output = Artisan::output();

        return view('pages.admin.temp_reservation_cleanup', [
            'output' => $output,
            'mode' => $apply ? 'apply' : 'dry-run',
            'error' => $error,
        ]);
    }
}
