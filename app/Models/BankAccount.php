<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  App\Models\Branch;
/**
 @property varchar $account_name account name @property varchar $account_no account no @property bigint $bank_branch_id bank branch id @property decimal $account_balance account balance @property enum $account_type account type @property tinyint $status status @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class BankAccount extends Model

{
    const ACCOUNT_TYPE_CURRENT = 'Current';
    const ACCOUNT_TYPE_SAVINGS = 'Savings';
    const ACCOUNT_TYPE_CREDIT = 'Credit';
    const ACCOUNT_TYPE_DOMICILIARY = 'Domiciliary';
    const ACCOUNT_TYPE_CASH = 'Cash';

    /**
     * Database table name
     */
    protected $table = 'bank_accounts';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['account_name', 'account_no', 'branch_id', 'account_balance', 'account_type', 'status'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id');
    }

}