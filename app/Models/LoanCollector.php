<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property varchar $address address @property varchar $email email @property varchar $phone phone @property varchar $reg_code reg code @property bigint $registered_by registered by @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class LoanCollector extends Model

{

    /**
     * Database table name
     */
    protected $table = 'loan_collectors';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name', 'address', 'email', 'phone', 'reg_code', 'registered_by'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function registered(){
        return $this->belongsTo(User::class,'registered_by');
    }
    public function loans(){
        return $this->hasMany(Loan::class);
    }

}