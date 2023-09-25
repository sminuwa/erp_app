<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Builder\Function_;

/**
 @property varchar $name name @property varchar $email email @property varchar $phone phone @property varchar $address address @property varchar $photo photo @property decimal $opening_balance opening balance @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Customer extends Model
{

    /**
     * Database table name
     */
    protected $table = 'customers';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name', 'code', 'email', 'phone', 'address', 'credit_limit'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class, 'customer_id');
    }
    public function amount()
    {
        return $this->hasMany(CustomerLedger::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function runningBalance()
    {
        return $this->ledgers()->sum('cr') - $this->ledgers->sum('dr');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}