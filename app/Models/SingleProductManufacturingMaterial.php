<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SingleProductManufacturingMaterial extends Model
{
    protected $table = 'single_product_manufacturing_materials';

    protected $fillable = [
        'manufacturing_id',
        'product_id',
        'store_id',
        'quantity',
        'unit_cost',
        'total_cost'
    ];

    public $timestamps = false;

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function manufacturing()
    {
        return $this->belongsTo(SingleProductManufacturing::class, 'manufacturing_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }
}
