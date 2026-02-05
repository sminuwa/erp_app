<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingReturnMaterial extends Model
{
    protected $table = 'manufacturing_return_materials';

    protected $fillable = [
        'return_id',
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

    public function return()
    {
        return $this->belongsTo(ManufacturingReturn::class, 'return_id', 'id');
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
