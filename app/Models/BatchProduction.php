<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BatchProduction extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_POSTED = 'posted';
    const STATUS_FULLY_CONVERTED = 'fully_converted';

    protected $table = 'batch_productions';

    protected $fillable = [
        'reference',
        'production_date',
        'requisition_id',
        'batch_number',
        'team_id',
        'machine_id',
        'quantity',
        'bom_id',
        'total_material_cost',
        'wip_value',
        'converted_qty',
        'remaining_qty',
        'branch_id',
        'created_by',
        'notes'
    ];

    protected $guarded = [
        'id',
        'status',
        'posted_by',
        'posted_at'
    ];

    protected $dates = [
        'production_date',
        'posted_at'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'total_material_cost' => 'decimal:2',
        'wip_value' => 'decimal:2',
        'converted_qty' => 'decimal:4',
        'remaining_qty' => 'decimal:4',
    ];

    public function requisition()
    {
        return $this->belongsTo(MaterialsRequisition::class, 'requisition_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo(ManufacturingTeam::class, 'team_id', 'id');
    }

    public function machine()
    {
        return $this->belongsTo(ManufacturingMachine::class, 'machine_id', 'id');
    }

    public function bom()
    {
        return $this->belongsTo(ManufacturingBom::class, 'bom_id', 'id');
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

    public function materials()
    {
        return $this->hasMany(BatchProductionMaterial::class, 'batch_production_id', 'id');
    }

    public function conversions()
    {
        return $this->hasMany(BatchConversion::class, 'batch_production_id', 'id');
    }

    public static function generateNewNumber($prefix = 'BPC', $length = 4)
    {
        return DB::transaction(function () use ($prefix, $length) {
            $prefix = $prefix . date('ym') . auth()->user()->branch->code;

            // Use lockForUpdate() to prevent race conditions
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

    public static function generateBatchNumber($length = 4)
    {
        $prefix = 'BATCH' . date('Ymd');
        $record = self::where('batch_number', 'like', $prefix . '%')->orderBy('batch_number', 'desc')->first();
        if (!$record) {
            $record = SingleProductManufacturing::where('batch_number', 'like', $prefix . '%')->orderBy('batch_number', 'desc')->first();
        }
        if ($record) {
            $number = $record->batch_number;
            $new = intval(substr($number, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPosted()
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function isFullyConverted()
    {
        return $this->status === self::STATUS_FULLY_CONVERTED;
    }

    public function canBeEdited()
    {
        return $this->isPending();
    }

    public function canBePosted()
    {
        return $this->isPending();
    }

    public function canBeDeleted()
    {
        return $this->isPending();
    }

    public function canBeConverted()
    {
        return $this->isPosted() && $this->remaining_qty > 0;
    }

    public function post()
    {
        if (!$this->canBePosted()) {
            return false;
        }

        // Calculate WIP value before saving (moved from controller)
        $bom = $this->bom;
        $otherCosts = ($bom->labor_cost + $bom->power_cost + $bom->other_cost) * $this->quantity;
        $this->wip_value = $this->total_material_cost + $otherCosts;

        $this->status = self::STATUS_POSTED;
        $this->remaining_qty = $this->quantity * $bom->actual_output;
        $this->posted_by = auth()->id();
        $this->posted_at = now();

        return $this->save();  // Single save with all fields
    }

    public function addConvertedQty($qty, $cost_deducted)
    {
        $this->converted_qty += $qty;
        $this->remaining_qty -= $qty;
        $this->wip_value -= $cost_deducted;

        if ($this->remaining_qty <= 0) {
            $this->status = self::STATUS_FULLY_CONVERTED;
        }

        return $this->save();
    }

    public function getWipCostPerUnit()
    {
        $totalExpectedOutput = $this->quantity * $this->bom->actual_output;
        return $totalExpectedOutput > 0 ? $this->wip_value / $totalExpectedOutput : 0;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeAvailableForConversion($query)
    {
        return $query->where('status', self::STATUS_POSTED)
            ->where('remaining_qty', '>', 0);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }

    public function scopeForTeam($query, $team_id = null)
    {
        return is_null($team_id) ? $query : $query->where('team_id', $team_id);
    }
}
