<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property varchar $class class
@property varchar $number number
@property varchar $description description
@property bigint $branch_id branch id
@property int $is_control is control
@property int $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class GeneralAccount extends Model
{

    /**
     * Database table name
     */
    protected $table = 'general_accounts';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'class',
        'number',
        'description',
        'branch_id',
        'is_control',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }


}