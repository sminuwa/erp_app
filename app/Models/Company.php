<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
@property varchar $name name
@property timestamp $created_at created at
@property timestamp $updated_at updated at
*/
class Company extends Model
{

    /**
     * Database table name
     */
    protected $table = 'companies';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function productS()
    {
        return $this->hasMany(Product::class);
    }
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }


}