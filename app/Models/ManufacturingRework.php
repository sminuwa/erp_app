<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManufacturingRework extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_POSTED = 'posted';

    const PRODUCTION_TYPE_SINGLE = 'single_product';
    const PRODUCTION_TYPE_BATCH = 'batch_conversion';

    protected $table = 'manufacturing_reworks';

    protected $fillable = [
        'reference',
        'rework_date',
        'production_type',
        'production_id',
        'labor_cost',
        'power_cost',
        'other_cost',
        'total_material_cost',
        'total_cost',
        'reason',
        'branch_id',
        'created_by'
    ];

    protected $guarded = [
        'id',
        'status',
        'posted_by',
        'posted_at'
    ];

    protected $dates = [
        'rework_date',
        'posted_at'
    ];

    protected $casts = [
        'labor_cost' => 'decimal:2',
        'power_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_material_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

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
        return $this->hasMany(ManufacturingReworkMaterial::class, 'rework_id', 'id');
    }

    /**
     * Get the single manufacturing record (only valid when production_type = 'single_product')
     */
    public function singleManufacturing()
    {
        return $this->belongsTo(SingleProductManufacturing::class, 'production_id', 'id');
    }

    /**
     * Get the batch conversion record (only valid when production_type = 'batch_conversion')
     */
    public function batchConversion()
    {
        return $this->belongsTo(BatchConversion::class, 'production_id', 'id');
    }

    public function getProduction()
    {
        if ($this->production_type === self::PRODUCTION_TYPE_SINGLE) {
            return SingleProductManufacturing::find($this->production_id);
        }
        return BatchConversion::find($this->production_id);
    }

    public static function generateNewNumber($prefix = 'MRW', $length = 4)
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

    public function calculateTotalMaterialCost()
    {
        return $this->materials()->sum('total_cost');
    }

    public function calculateTotalOtherCost()
    {
        return $this->labor_cost + $this->power_cost + $this->other_cost;
    }

    public function calculateTotalCost()
    {
        return $this->total_material_cost + $this->calculateTotalOtherCost();
    }

    public function recalculateCosts()
    {
        $this->total_material_cost = $this->calculateTotalMaterialCost();
        $this->total_cost = $this->calculateTotalCost();
        return $this->save();
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
