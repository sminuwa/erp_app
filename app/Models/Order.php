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
    public function createdBy()
    {
        return $this->belongsTo(User::class,'sold_by');
    }
    public function sold()
    {
        return $this->belongsTo(User::class, 'sold_by', 'id');
    }
    public function issued()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }
    public function amount()
    {
        return $this->hasMany(CustomerLedger::class);
    }
    public function order_items()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function branch()
    {
        return $this->order_items()->first()->branch();
    }

    public static function generateNewNumber($prefix = 'INV', $length = 4)
    {
        $prefix = $prefix . date('ym') . auth()->user()->branch->code;
        $record = self::where('reference', 'like', '%' . $prefix . '%')->orderBy('reference', 'desc')->first();
        if ($record) {
            $number = $record->reference;
            $new = intval(substr($number, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }
    public function order_invoice()
    {
        return $this->belongsTo(OrderInvoice::class, 'order_invoice_id');
    }
    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class, 'order_id');
    }

    /*public function scope*/

}
