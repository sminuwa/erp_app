<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property varchar $phone phone @property varchar $email email @property varchar $address address @property tinyint $status status @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Branch extends Model

{

    /**
     * Database table name
     */
    protected $table = 'branches';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name', 'code', 'status'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function stores(){
        return $this->hasMany(Store::class,'branch_id','id');
    }


}