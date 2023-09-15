<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $loan_collector_id loan collector id @property decimal $amount amount @property enum $payment_mode payment mode @property bigint $bank_account_id bank account id @property date $date date @property bigint $granted_by granted by @property varchar $receipt_no receipt no @property date $due_date due date @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Loan extends Model

{
    const PAYMENT_MODE_CASH = 'Cash';
    const PAYMENT_MODE_CHEQUE = 'Cheque';

    /**
     * Database table name
     */
    protected $table = 'loans';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['loan_collector_id', 'amount', 'payment_mode', 'bank_account_id', 'date', 'granted_by', 'receipt_no', 'due_date'];

    /**
     * Date time columns.
     */
    protected $dates = ['date', 'due_date'];

    public function collector(){
        return $this->belongsTo(LoanCollector::class,'loan_collector_id');
    }
    public function bankAccount(){
        return $this->belongsTo(BankAccount::class,'bank_account_id');
    }
    public function granted(){
        return $this->belongsTo(User::class,'granted_by','id');
    }

 public function payments(){
        return $this->hasMany(LoanPayment::class,'loan_id');
    }
}