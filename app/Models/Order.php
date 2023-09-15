<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $customer_id customer id @property varchar $order_date order date @property varchar $order_status order status @property int $total_products total products @property double $sub_total sub total @property double $vat vat @property double $total total @property varchar $payment_status payment status @property double $pay pay @property double $due due @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Order extends Model

{

    /**
     * Database table name
     */
    protected $table = 'orders';

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
        return $this->hasMany(OrderDetail::class);
    }
    public function branch(){
        return $this->order_items()->first()->branch();
    }
}