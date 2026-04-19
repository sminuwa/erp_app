<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingWorkOrderItem extends Model
{
    protected $table = 'manufacturing_work_order_items';

    protected $fillable = [
        'work_order_id',
        'schedule_item_id',
        'planned_qty',
    ];

    protected $casts = [
        'planned_qty' => 'decimal:4',
    ];

    public function workOrder()
    {
        return $this->belongsTo(ManufacturingWorkOrder::class, 'work_order_id', 'id');
    }

    public function scheduleItem()
    {
        return $this->belongsTo(DailyManufacturingScheduleItem::class, 'schedule_item_id', 'id');
    }
}
