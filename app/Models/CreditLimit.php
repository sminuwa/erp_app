<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $customer_id customer id
@property decimal $amount amount
@property tinyint $status status
@property bigint $updated_by updated by
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class CreditLimit extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'credit_limits';

    /**
    * Mass assignable columns
    */
    protected $fillable=['customer_id',
'amount',
'status',
'updated_by'];

    /**
    * Date time columns.
    */
    protected $dates=[];

public function customer(){
    return $this->belongsTo(Customer::class);
}

public function user(){
    return $this->belongsTo(User::class,'updated_by');
}
}