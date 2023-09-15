<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property varchar $email email @property varchar $phone phone @property varchar $address address @property varchar $experience experience @property varchar $photo photo @property varchar $salary salary @property varchar $vacation vacation @property varchar $city city @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Employee extends Model

{

    /**
     * Database table name
     */
    protected $table = 'employees';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name', 'email', 'phone', 'address', 'experience', 'salary', 'city'];

    /**
     * Date time columns.
     */
    protected $dates = [];




}