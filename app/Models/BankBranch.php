<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property varchar $sortcode sortcode @property bigint $bank_id bank id @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class BankBranch extends Model

{

    /**
     * Database table name
     */
    protected $table = 'bank_branches';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name', 'sortcode', 'bank_id'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function bank(){
        return $this->belongsTo(Bank::class,'bank_id','id');
    }


}