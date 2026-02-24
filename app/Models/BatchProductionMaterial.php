<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchProductionMaterial extends Model
{
    protected $table = 'batch_production_materials';

    protected $fillable = [
        'batch_production_id',
        'product_id',
        'store_id',
        'quantity',
    ];

    public $timestamps = false;

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function batchProduction()
    {
        return $this->belongsTo(BatchProduction::class, 'batch_production_id', 'id');
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
