<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingBomMaterial extends Model
{
    protected $table = 'manufacturing_bom_materials';

    protected $fillable = [
        'bom_id',
        'product_id',
        'quantity',
        'source_store_id',
        'unit_cost',
        'total_cost'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function bom()
    {
        return $this->belongsTo(ManufacturingBom::class, 'bom_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sourceStore()
    {
        return $this->belongsTo(Store::class, 'source_store_id', 'id');
    }

    public function calculateTotalCost()
    {
        return $this->quantity * $this->unit_cost;
    }

    public function updateCosts($unit_cost = null)
    {
        if ($unit_cost !== null) {
            $this->unit_cost = $unit_cost;
        }
        $this->total_cost = $this->calculateTotalCost();
        return $this->save();
    }
}
