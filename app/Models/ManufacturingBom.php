<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ManufacturingBom extends Model
{
    const TYPE_BATCH = 'batch';
    const TYPE_SINGLE = 'single';

    protected $table = 'manufacturing_boms';

    protected $fillable = [
        'reference',
        'approval_document_no',
        'category_id',
        'description',
        'bom_type',
        'finish_product_id',
        'output_store_id',
        'actual_output',
        'accepted_excess',
        'accepted_shortage',
        'main_raw_material_id',
        'labor_cost',
        'power_cost',
        'other_cost',
        'total_material_cost',
        'total_other_cost',
        'total_cost_per_unit',
        'margin_per_piece',
        'margin_gl_account_id',
        'branch_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'actual_output' => 'decimal:4',
        'accepted_excess' => 'decimal:4',
        'accepted_shortage' => 'decimal:4',
        'labor_cost' => 'decimal:2',
        'power_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_material_cost' => 'decimal:2',
        'total_other_cost' => 'decimal:2',
        'total_cost_per_unit' => 'decimal:2',
        'margin_per_piece' => 'decimal:4',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function finishProduct()
    {
        return $this->belongsTo(Product::class, 'finish_product_id', 'id');
    }

    public function outputStore()
    {
        return $this->belongsTo(Store::class, 'output_store_id', 'id');
    }

    public function mainRawMaterial()
    {
        return $this->belongsTo(Product::class, 'main_raw_material_id', 'id');
    }

    public function marginGlAccount()
    {
        return $this->belongsTo(GeneralAccount::class, 'margin_gl_account_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function materials()
    {
        return $this->hasMany(ManufacturingBomMaterial::class, 'bom_id', 'id');
    }

    public static function generateNewNumber($prefix = 'BOM', $length = 4)
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

    public function calculateTotalMaterialCost()
    {
        return $this->materials()->sum(DB::raw('quantity * unit_cost'));
    }

    public function calculateTotalOtherCost()
    {
        return $this->labor_cost + $this->power_cost + $this->other_cost;
    }

    public function calculateTotalCostPerUnit()
    {
        $total = $this->total_material_cost + $this->total_other_cost;
        return $this->actual_output > 0 ? $total / $this->actual_output : 0;
    }

    public function recalculateCosts()
    {
        $this->total_material_cost = $this->calculateTotalMaterialCost();
        $this->total_other_cost = $this->calculateTotalOtherCost();
        $this->total_cost_per_unit = $this->calculateTotalCostPerUnit();
        return $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }

    public function scopeBatch($query)
    {
        return $query->where('bom_type', self::TYPE_BATCH);
    }

    public function scopeSingle($query)
    {
        return $query->where('bom_type', self::TYPE_SINGLE);
    }

    public function isBatch()
    {
        return $this->bom_type === self::TYPE_BATCH;
    }

    public function isSingle()
    {
        return $this->bom_type === self::TYPE_SINGLE;
    }
}
