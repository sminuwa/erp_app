<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyManufacturingScheduleItem extends Model
{
    protected $table = 'daily_manufacturing_schedule_items';

    protected $fillable = [
        'schedule_id',
        'production_order_item_id',
        'scheduled_qty',
        'requisitioned_qty'
    ];

    protected $casts = [
        'scheduled_qty' => 'decimal:4',
        'requisitioned_qty' => 'decimal:4',
    ];

    public function schedule()
    {
        return $this->belongsTo(DailyManufacturingSchedule::class, 'schedule_id', 'id');
    }

    public function productionOrderItem()
    {
        return $this->belongsTo(ProductionOrderItem::class, 'production_order_item_id', 'id');
    }

    public function getUnrequisitionedQty()
    {
        return $this->scheduled_qty - $this->requisitioned_qty;
    }

    public function hasUnrequisitionedQty()
    {
        return $this->getUnrequisitionedQty() > 0;
    }

    public function addRequisitionedQty($qty)
    {
        $this->requisitioned_qty += $qty;
        return $this->save();
    }
}
