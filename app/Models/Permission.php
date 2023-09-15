<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $name name
@property varchar $guard_name guard name
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property \Illuminate\Database\Eloquent\Collection $modelhass belongsToMany
@property \Illuminate\Database\Eloquent\Collection $rolehass belongsToMany
   
 */
class Permission extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'permissions';

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
        return $this->belongsToMany(Modelhass::class,'model_has_permissions');
    }
    /**
    * rolehasses
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    */
    public function rolehasses()
    {
        return $this->belongsToMany(Rolehass::class,'role_has_permissions');
    }



}