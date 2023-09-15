<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
/**
 @property decimal $amount amount @property bigint $source_account_id source account id @property bigint $destination_account_id destination account id @property bigint $withdraw_by withdraw by @property bigint $deposited_by deposited by @property bigint $sent_by sent by @property enum $type type @property date $date date @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class CashMovement extends Model

{
    const TYPE_DEPOSIT = 'Deposit';
    const TYPE_WITHDRAWAL = 'Withdrawal';
    const TYPE_BOTH = 'Both';

    /**
     * Database table name
     */
    protected $table = 'cash_movements';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['amount', 'source_account_id', 'destination_account_id', 'withdraw_by', 'deposited_by', 'sent_by', 'type', 'date'];

    /**
     * Date time columns.
     */
    protected $dates = ['date'];

    public function withdrawer()
    {
        return $this->belongsTo(User::class , 'withdraw_by', 'id');
    }
    public function depositor()
    {
        return $this->belongsTo(User::class , 'deposited_by', 'id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class , 'sent_by', 'id');
    }
    public function capturedBy()
    {
        return $this->belongsTo(User::class , 'captured_by', 'id');
    }
    public function fromAccount()
    {
        return $this->belongsTo(BankAccount::class , 'source_account_id', 'id');
    }
    public function toAccount()
    {
        return $this->belongsTo(BankAccount::class , 'destination_account_id', 'id');
    }

}