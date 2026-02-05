<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderItem extends Model
{
    protected $table = 'production_order_items';

    protected $fillable = [
        'production_order_id',
        'bom_id',
        'quantity_to_produce',
        'scheduled_qty',
        'produced_qty'
    ];

    protected $casts = [
        'quantity_to_produce' => 'decimal:4',
        'scheduled_qty' => 'decimal:4',
        'produced_qty' => 'decimal:4',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id', 'id');
    }

    public function bom()
    {
        return $this->belongsTo(ManufacturingBom::class, 'bom_id', 'id');
    }

    public function materials()
    {
        return $this->hasMany(ProductionOrderMaterial::class, 'production_order_item_id', 'id');
    }

    public function scheduleItems()
    {
        return $this->hasMany(DailyManufacturingScheduleItem::class, 'production_order_item_id', 'id');
    }

    public function getUnscheduledQty()
    {
        return $this->quantity_to_produce - $this->scheduled_qty;
    }

    public function getUnproducedQty()
    {
        return $this->quantity_to_produce - $this->produced_qty;
    }

    public function hasUnscheduledQty()
    {
        return $this->getUnscheduledQty() > 0;
    }

    public function addScheduledQty($qty)
    {
        $this->scheduled_qty += $qty;
        return $this->save();
    }

    public function addProducedQty($qty)
    {
        $this->produced_qty += $qty;
        return $this->save();
    }
}
