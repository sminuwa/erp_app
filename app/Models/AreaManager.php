<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property int $manager_id Manager Id
@property int $branch_id Branch Id
@property int $status Status
@property \Carbon\Carbon $created_at Created At
@property \Carbon\Carbon $updated_at Updated At
@property User $user BelongsTo
@property Branch $branch BelongsTo
   
 */
class AreaManager extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'area_managers';

    /**
    * Mass assignable columns
    */
    protected $fillable=[		'manager_id',
		'branch_id',
		'status'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    /**
    * user
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relationship with Branch
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Relationship to get all branches assigned to a manager
     */
    public function branches()
    {
        return $this->hasMany(AreaManager::class, 'manager_id', 'manager_id');
    }
}