<?php

namespace App\Console\Commands;

use App\Models\BatchProduction;
use App\Models\DailyManufacturingSchedule;
use App\Models\InventoryReservation;
use App\Models\MaterialsRequisition;
use App\Models\SingleProductManufacturing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupStaleScheduleReservations extends Command
{
    protected $signature = 'manufacturing:cleanup-stale-schedule-reservations {--apply : Actually update rows. Without this flag the command runs in dry-run mode.}';

    protected $description = 'Marks stale daily_schedule inventory_reservations as consumed when their schedule is fully manufactured.';

    public function handle()
    {
        $apply = (bool) $this->option('apply');

        $this->line($apply ? '=== APPLY MODE ===' : '=== DRY RUN ===');

        $rows = InventoryReservation::where('reference_type', 'daily_schedule')
            ->where('status', InventoryReservation::STATUS_RESERVED)
            ->get();

        $this->line('Reservations examined: ' . $rows->count());

        $toConsumeIds = [];
        $byProduct = [];
        $skippedMissing = 0;
        $skippedInFlight = 0;

        foreach ($rows as $row) {
            $schedule = DailyManufacturingSchedule::with('items')->find($row->reference_id);

            if (!$schedule) {
                $skippedMissing++;
                continue;
            }

            if (!$this->scheduleIsFullyManufactured($schedule)) {
                $skippedInFlight++;
                continue;
            }

            $toConsumeIds[] = $row->id;
            $key = $row->product_id . ':' . $row->store_id;
            if (!isset($byProduct[$key])) {
                $byProduct[$key] = ['product_id' => $row->product_id, 'store_id' => $row->store_id, 'qty' => 0, 'rows' => 0];
            }
            $byProduct[$key]['qty'] += (float) $row->reserved_qty;
            $byProduct[$key]['rows']++;
        }

        $this->line('');
        $this->line('Would consume: ' . count($toConsumeIds) . ' rows');
        $this->line('Skipped (schedule missing): ' . $skippedMissing);
        $this->line('Skipped (schedule still in-flight): ' . $skippedInFlight);
        $this->line('');

        if (!empty($byProduct)) {
            $this->line('Per (product_id, store_id) summary:');
            $this->line(str_pad('product_id', 12) . str_pad('store_id', 10) . str_pad('rows', 8) . 'reserved_qty');
            foreach ($byProduct as $g) {
                $this->line(
                    str_pad((string) $g['product_id'], 12) .
                    str_pad((string) $g['store_id'], 10) .
                    str_pad((string) $g['rows'], 8) .
                    rtrim(rtrim(number_format($g['qty'], 4, '.', ''), '0'), '.')
                );
            }
            $this->line('');
        }

        if (!$apply) {
            $this->line('Dry run only. Re-run with --apply to update rows.');
            return self::SUCCESS;
        }

        if (empty($toConsumeIds)) {
            $this->line('Nothing to update.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($toConsumeIds) {
            InventoryReservation::whereIn('id', $toConsumeIds)
                ->update(['status' => InventoryReservation::STATUS_CONSUMED]);
        });

        $this->line('Updated ' . count($toConsumeIds) . ' rows to status=consumed.');
        return self::SUCCESS;
    }

    protected function scheduleIsFullyManufactured(DailyManufacturingSchedule $schedule): bool
    {
        $requisition = MaterialsRequisition::where('schedule_id', $schedule->id)->first();
        if (!$requisition) {
            return false;
        }

        $postedBatchQty = (float) BatchProduction::where('requisition_id', $requisition->id)
            ->where('status', BatchProduction::STATUS_POSTED)
            ->sum('quantity');

        $postedSpmQty = (float) SingleProductManufacturing::where('requisition_id', $requisition->id)
            ->where('status', SingleProductManufacturing::STATUS_POSTED)
            ->sum('quantity');

        $producedTotal = $postedBatchQty + $postedSpmQty;

        $scheduledTotal = (float) $schedule->items->sum('scheduled_qty');

        return $scheduledTotal > 0 && $producedTotal >= $scheduledTotal;
    }
}
