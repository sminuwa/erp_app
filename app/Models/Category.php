<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Category extends Model

{

    /**
     * Database table name
     */
    protected $table = 'categories';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name','code'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function products()
    {
        return $this->hasMany(Product::class);
    }


}