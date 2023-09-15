<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;
/**
   @property varchar $name name
@property tinyint $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class PaymentMode extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'payment_modes';

    /**
    * Mass assignable columns
    */
    protected $fillable=['name',
'status'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    public function expenses(){
        return $this->hasMany(Expense::class);
    }


}