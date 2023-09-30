<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderInvoice extends Model
{
    use HasFactory;

    protected $table = 'order_invoices';
    /**
     * Mass assignable columns
     */
    protected $fillable = ['customer_id', 'order_date', 'order_status', 'total_products', 'sub_total', 'vat', 'total', 'payment_status', 'pay', 'due'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function sold()
    {
        return $this->belongsTo(User::class , 'sold_by', 'id');
    }
    public function issued()
    {
        return $this->belongsTo(User::class , 'issued_by', 'id');
    }
    public function amount()
    {
        return $this->hasMany(CustomerLedger::class);
    }
    public function order_items()
    {
        return $this->hasMany(OrderInvoiceDetail::class,'order_id');
    }
    public function branch(){
        return $this->order_items()->first()->branch();
    }
}
