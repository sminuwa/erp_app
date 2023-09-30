<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
/**
 @property varchar $name name @property varchar $code code @property bigint $created_by created by @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class ExpenseItem extends Model

{

    /**
     * Database table name
     */
    protected $table = 'expense_items';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name', 'code', 'created_by'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }




}
