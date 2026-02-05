<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderMaterial extends Model
{
    protected $table = 'production_order_materials';

    protected $fillable = [
        'production_order_id',
        'production_order_item_id',
        'product_id',
        'required_qty',
        'source_store_id',
        'unit_cost',
        'total_cost'
    ];

    public $timestamps = false;

    protected $casts = [
        'required_qty' => 'decimal:4',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id', 'id');
    }

    public function productionOrderItem()
    {
        return $this->belongsTo(ProductionOrderItem::class, 'production_order_item_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sourceStore()
    {
        return $this->belongsTo(Store::class, 'source_store_id', 'id');
    }
}
