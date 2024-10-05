<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $user_id user id
@property bigint $company_id company id
@property bigint $created_by created by
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property CreatedBy $user belongsTo
@property Company $company belongsTo
@property User $user belongsTo
   
 */
class UserAccessSite extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'user_access_sites';

    /**
    * Mass assignable columns
    */
    protected $fillable=['user_id',
'company_id',
'created_by'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    /**
    * createdBy
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    /**
    * company
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function company()
    {
        return $this->belongsTo(Company::class,'company_id');
    }

    /**
    * user
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }




}