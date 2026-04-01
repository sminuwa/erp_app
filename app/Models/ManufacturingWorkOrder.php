<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Classes\Manufacturing\ProductionCalculator;

class ManufacturingWorkOrder extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_POSTED = 'posted';

    protected $table = 'manufacturing_work_orders';

    protected $fillable = [
        'reference',
        'work_order_date',
        'daily_schedule_id',
        'machine_id',
        'team_id',
        'notes',
        'branch_id',
        'created_by',
    ];

    protected $guarded = [
        'id',
        'status',
        'posted_by',
        'posted_at',
    ];

    protected $dates = [
        'work_order_date',
        'posted_at',
    ];

    // ── Relationships ──────────────────────────────────────

    public function schedule()
    {
        return $this->belongsTo(DailyManufacturingSchedule::class, 'daily_schedule_id', 'id');
    }

    public function machine()
    {
        return $this->belongsTo(ManufacturingMachine::class, 'machine_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(ManufacturingTeam::class, 'team_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(ManufacturingWorkOrderItem::class, 'work_order_id', 'id');
    }

    public function requisitions()
    {
        return $this->hasMany(MaterialsRequisition::class, 'work_order_id', 'id');
    }

    // ── Reference Generator ────────────────────────────────

    public static function generateNewNumber($prefix = 'WOR', $length = 4)
    {
        return DB::transaction(function () use ($prefix, $length) {
            $prefix = $prefix . date('ym') . auth()->user()->branch->code;

            $record = self::where('reference', 'like', $prefix . '%')
                ->orderBy('reference', 'desc')
                ->lockForUpdate()
                ->first();

            if ($record) {
                $number = $record->reference;
                $new = intval(substr($number, strlen($prefix))) + 1;
                return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
            }
            return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
        });
    }

    // ── Status Checks ──────────────────────────────────────

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPosted()
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function canBePosted()
    {
        return $this->isPending();
    }

    public function canBeEdited()
    {
        return $this->isPending();
    }

    // ── State Transitions ──────────────────────────────────

    public function post()
    {
        if (!$this->canBePosted()) {
            return false;
        }

        $this->status = self::STATUS_POSTED;
        $this->posted_by = auth()->id();
        $this->posted_at = now();
        return $this->save();
    }

    // ── Material Calculations ──────────────────────────────

    public function getRequiredMaterials()
    {
        $this->load(['items.scheduleItem.productionOrderItem.bom']);

        $allMaterials = [];
        foreach ($this->items as $item) {
            $bomId = $item->scheduleItem->productionOrderItem->bom_id;
            $materialsData = ProductionCalculator::calculateMaterials(
                $bomId,
                $item->planned_qty,
                $this->branch_id
            );

            foreach ($materialsData['materials'] as $material) {
                $key = $material['product_id'] . '_' . $material['store_id'];
                if (!isset($allMaterials[$key])) {
                    $allMaterials[$key] = $material;
                } else {
                    $allMaterials[$key]['quantity'] += $material['quantity'];
                    $allMaterials[$key]['total_cost'] += $material['total_cost'];
                }
            }
        }

        return array_values($allMaterials);
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }
}
