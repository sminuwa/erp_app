<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $customer_id customer id @property varchar $systemid systemid @property varchar $description description @property varchar $Ref Ref @property decimal $cr cr @property decimal $dr dr @property date $date date @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class CustomerLedger extends Model

{

    /**
     * Database table name
     */
    protected $table = 'customer_ledgers';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['customer_id', 'systemid', 'description', 'Ref', 'cr', 'dr', 'date'];

    /**
     * Date time columns.
     */
    protected $dates = ['date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class , 'bank_account_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class , 'order_id', 'id');
    }

}