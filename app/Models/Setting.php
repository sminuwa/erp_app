<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $name name
@property varchar $address address
@property varchar $email email
@property varchar $phone phone
@property varchar $mobile mobile
@property varchar $logo logo
@property varchar $city city
@property varchar $country country
@property int $zip_code zip code
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Setting extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'settings';

    /**
    * Mass assignable columns
    */
    protected $fillable=['name',
'address',
'email',
'phone',
'mobile',
'logo',
'city',
'country',
'zip_code'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}