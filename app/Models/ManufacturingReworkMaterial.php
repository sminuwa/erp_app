<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingReworkMaterial extends Model
{
    protected $table = 'manufacturing_rework_materials';

    protected $fillable = [
        'rework_id',
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

    public function rework()
    {
        return $this->belongsTo(ManufacturingRework::class, 'rework_id', 'id');
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
