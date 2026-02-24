<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SingleProductManufacturing extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_POSTED = 'posted';

    protected $table = 'single_product_manufacturing';

    protected $fillable = [
        'reference',
        'manufacturing_date',
        'requisition_id',
        'batch_number',
        'team_id',
        'machine_id',
        'quantity',
        'bom_id',
        'finish_product_id',
        'output_store_id',
        'total_material_cost',
        'labor_cost',
        'power_cost',
        'other_cost',
        'total_other_cost',
        'total_cost',
        'unit_cost',
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
        'manufacturing_date',
        'posted_at'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'total_material_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'power_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_other_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'unit_cost' => 'decimal:2',
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

    public function finishProduct()
    {
        return $this->belongsTo(Product::class, 'finish_product_id', 'id');
    }

    public function outputStore()
    {
        return $this->belongsTo(Store::class, 'output_store_id', 'id');
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
        return $this->hasMany(SingleProductManufacturingMaterial::class, 'manufacturing_id', 'id');
    }

    public function additionalCosts()
    {
        return ManufacturingAdditionalCost::where('production_type', 'single_product')
            ->where('production_id', $this->id);
    }

    public function returns()
    {
        return ManufacturingReturn::where('production_type', 'single_product')
            ->where('production_id', $this->id);
    }

    public function reworks()
    {
        return ManufacturingRework::where('production_type', 'single_product')
            ->where('production_id', $this->id);
    }

    public static function generateNewNumber($prefix = 'SPM', $length = 4)
    {
        return DB::transaction(function () use ($prefix, $length) {
            $prefix = $prefix . date('ym') . auth()->user()->branch->code;
            $record = self::where('reference', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('reference', 'desc')
                ->first();
            if ($record) {
                $new = intval(substr($record->reference, strlen($prefix))) + 1;
                return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
            }
            return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
        });
    }

    public static function generateBatchNumber($length = 4)
    {
        $prefix = 'BATCH' . date('Ymd');
        $record = self::where('batch_number', 'like', $prefix . '%')->orderBy('batch_number', 'desc')->first();
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

    public function calculateUnitCost()
    {
        return $this->quantity > 0 ? $this->total_cost / $this->quantity : 0;
    }

    public function getTotalReturnedQty()
    {
        return ManufacturingReturn::where('production_type', 'single_product')
            ->where('production_id', $this->id)
            ->whereIn('status', ['pending', 'posted'])
            ->sum('return_qty');
    }

    public function getAvailableReturnQty()
    {
        return $this->quantity - $this->getTotalReturnedQty();
    }

    /**
     * Alias for getAvailableReturnQty() - used by ManufacturingReturnController
     */
    public function getRemainingReturnableQty()
    {
        return $this->getAvailableReturnQty();
    }

    /**
     * Get the unit cost of this production
     */
    public function getUnitCost()
    {
        return $this->unit_cost ?? $this->calculateUnitCost();
    }

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

    public function scopeForTeam($query, $team_id = null)
    {
        return is_null($team_id) ? $query : $query->where('team_id', $team_id);
    }
}
