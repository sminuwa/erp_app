<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoiceDetail extends Model
{
    use HasFactory;
    protected $table = 'order_invoice_details';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_cost', 'total'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function order()
    {
        return $this->belongsTo(OrderInvoice::class);
    }
}
