<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $code code
@property bigint $general_account_id general account id
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class GeneralAccountControl extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'general_account_controls';

    /**
    * Mass assignable columns
    */
    protected $fillable=['code',
'general_account_id'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}