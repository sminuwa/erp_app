<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $reference reference
@property decimal $amount amount
@property bigint $created_by created by
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Journal extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'journals';

    /**
    * Mass assignable columns
    */
    protected $fillable=['reference',
'amount',
'created_by'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}