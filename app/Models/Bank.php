<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $name name
@property varchar $abbreviation abbreviation
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Bank extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'banks';

    /**
    * Mass assignable columns
    */
    protected $fillable=['name',
'abbreviation'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}