<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialsRequisitionItem extends Model
{
    protected $table = 'materials_requisition_items';

    protected $fillable = [
        'requisition_id',
        'product_id',
        'source_store_id',
        'required_qty',
        'issued_qty',
        'received_qty',
        'unit_cost',
        'total_cost'
    ];

    protected $casts = [
        'required_qty' => 'decimal:4',
        'issued_qty' => 'decimal:4',
        'received_qty' => 'decimal:4',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function requisition()
    {
        return $this->belongsTo(MaterialsRequisition::class, 'requisition_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sourceStore()
    {
        return $this->belongsTo(Store::class, 'source_store_id', 'id');
    }

    public function getTotalCost()
    {
        return $this->required_qty * $this->unit_cost;
    }

    public function getUnissuedQty()
    {
        return $this->required_qty - $this->issued_qty;
    }

    public function getUnreceivedQty()
    {
        return $this->issued_qty - $this->received_qty;
    }
}
