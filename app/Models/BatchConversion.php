<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BatchConversion extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_POSTED = 'posted';

    protected $table = 'batch_conversions';

    protected $fillable = [
        'reference',
        'conversion_date',
        'batch_production_id',
        'produced_qty',
        'finish_product_id',
        'output_store_id',
        'wip_cost_deducted',
        'labor_cost',
        'power_cost',
        'other_cost',
        'total_cost',
        'unit_cost',
        'branch_id',
        'created_by'
    ];

    protected $guarded = ['id', 'status', 'posted_by', 'posted_at'];

    protected $dates = [
        'conversion_date',
        'posted_at'
    ];

    protected $casts = [
        'produced_qty' => 'decimal:4',
        'wip_cost_deducted' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'power_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'unit_cost' => 'decimal:2',
    ];

    public function batchProduction()
    {
        return $this->belongsTo(BatchProduction::class, 'batch_production_id', 'id');
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

    public function additionalCosts()
    {
        return ManufacturingAdditionalCost::where('production_type', 'batch_conversion')
            ->where('production_id', $this->id);
    }

    public function returns()
    {
        return ManufacturingReturn::where('production_type', 'batch_conversion')
            ->where('production_id', $this->id);
    }

    public function reworks()
    {
        return ManufacturingRework::where('production_type', 'batch_conversion')
            ->where('production_id', $this->id);
    }

    public static function generateNewNumber($prefix = 'BCN', $length = 4)
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

    public function calculateTotalCost()
    {
        return $this->wip_cost_deducted + $this->labor_cost + $this->power_cost + $this->other_cost;
    }

    public function calculateUnitCost()
    {
        return $this->produced_qty > 0 ? $this->total_cost / $this->produced_qty : 0;
    }

    public function getTotalReturnedQty()
    {
        return ManufacturingReturn::where('production_type', 'batch_conversion')
            ->where('production_id', $this->id)
            ->where('status', 'posted')
            ->sum('return_qty');
    }

    public function getAvailableReturnQty()
    {
        return $this->produced_qty - $this->getTotalReturnedQty();
    }

    /**
     * Alias for getAvailableReturnQty() - used by ManufacturingReturnController
     */
    public function getRemainingReturnableQty()
    {
        return $this->getAvailableReturnQty();
    }

    /**
     * Get the unit cost of this conversion
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
}
