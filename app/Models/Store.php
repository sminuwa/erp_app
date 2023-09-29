<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property bigint $branch_id branch id @property tinyint $status status @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class Store extends Model

{

    /**
     * Database table name
     */
    protected $table = 'stores';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name','code', 'branch_id', 'status'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function storeProducts(){
        return $this->hasMany(StoreProduct::class,'product_id','id');
    }

    public function scopeForBranch($query, int $branch_id = null){
        if(is_null($branch_id))
            return $query;
        return $query->where('branch_id', $branch_id);
    }

}
