<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bigint $journal_id journal id
 * @property bigint $account_id account id
 * @property varchar $account_type account type
 * @property decimal $credit credit
 * @property decimal $debit debit
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 */
class JournalItem extends Model
{

    /**
     * Database table name
     */
    protected $table = 'journal_items';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['journal_id',
        'account_id',
        'account_type',
        'credit',
        'debit',
        'description'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function account(){
        if($this->account_type == 'Customer')
            return Customer::find($this->account_id);
        if($this->account_type == 'Supplier')
            return Supplier::find($this->account_id);
        if($this->account_type == 'GeneralAccount')
            return GeneralAccount::find($this->account_id);
    }

    public static function deleteItems($journal_id){

        if(self::where('journal_id', $journal_id)->delete()){
            return true;
        }
        return false;
    }

}
