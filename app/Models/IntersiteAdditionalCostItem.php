<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntersiteAdditionalCostItem extends Model
{
    protected $table = 'intersite_additional_cost_items';

    protected $fillable = [
        'intersite_additional_cost_id',
        'intersite_transfer_product_id',
        'product_id',
        'quantity',
        'original_cost',
        'allocated_amount',
        'additional_unit_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:6',
        'original_cost' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'additional_unit_cost' => 'decimal:4',
    ];

    public function additionalCost()
    {
        return $this->belongsTo(IntersiteAdditionalCost::class, 'intersite_additional_cost_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function intersiteTransferProduct()
    {
        return $this->belongsTo(IntersiteTransferProduct::class, 'intersite_transfer_product_id', 'id');
    }
}
