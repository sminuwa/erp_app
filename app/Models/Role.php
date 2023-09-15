<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $name name
@property varchar $guard_name guard name
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property \Illuminate\Database\Eloquent\Collection $modelhass belongsToMany
@property \Illuminate\Database\Eloquent\Collection $haspermission belongsToMany
   
 */
class Role extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'roles';

    /**
    * Mass assignable columns
    */
    protected $fillable=['name'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    /**
    * modelhasses
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function modelhasses()
    {
        return $this->belongsToMany(Modelhass::class,'model_has_roles');
    }
    /**
    * haspermissions
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function haspermissions()
    {
        return $this->belongsToMany(Permission::class,'role_has_permissions');
    }



}