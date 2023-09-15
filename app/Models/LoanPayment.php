<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
/**
 @property bigint $loan_id loan id @property decimal $amount amount @property enum $payment_mode payment mode @property bigint $bank_account_id bank account id @property varchar $cheque_no cheque no @property varchar $receipt_no receipt no @property decimal $received_by received by @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class LoanPayment extends Model

{
    const PAYMENT_MODE_CASH = 'Cash';
    const PAYMENT_MODE_CHEQUE = 'Cheque';

    /**
     * Database table name
     */
    protected $table = 'loan_payments';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['loan_id', 'amount', 'payment_mode', 'bank_account_id', 'cheque_no', 'receipt_no', 'received_by'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function loan(){
        return $this->belongsTo(Loan::class,'loan_id');
    }

    public function bankAccount(){
        return $this->belongsTo(BankAccount::class);
    }
    public function received(){
        return $this->belongsTo(User::class,'received_by','id');
    }
}