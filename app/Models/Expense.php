<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $expense_item_id expense item id @property varchar $name name @property double $amount amount @property varchar $month month @property varchar $year year @property varchar $date date @property bigint $payment_mode_id payment mode id @property varchar $impress impress @property varchar $account_name account name @property varchar $reason reason @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Expense extends Model

{

    /**
     * Database table name
     */
    protected $table = 'expenses';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['expense_item_id',        'captured_by',        'amount',        'month',        'year',        'date',        'payment_mode_id',        'impress',        'bank_account_id',        'reason','status'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function mode()
    {
        return $this->belongsTo(PaymentMode::class,'payment_mode_id','id');    
    }
    public function user(){
        return $this->belongsTo(User::class,'captured_by','id');
    }
    public function item(){
        return $this->belongsTo(ExpenseItem::class,'expense_item_id');
    }
    public function account(){
        return $this->belongsTo(BankAccount::class,'bank_account_id');
    }
}