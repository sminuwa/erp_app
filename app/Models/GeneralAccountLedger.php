<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property bigint $model_id model id
@property varchar $model_name model name
@property bigint $branch_id branch id
@property varchar $description description
@property varchar $reference reference
@property decimal $credit credit
@property decimal $debit debit
@property date $date date
@property bigint $user_id user id
@property varchar $receipt_no receipt no
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class GeneralAccountLedger extends Model
{

    /**
     * Database table name
     */
    protected $table = 'general_account_ledgers';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'model_id',
        'model_name',
        'branch_id',
        'description',
        'reference',
        'credit',
        'debit',
        'date',
        'user_id',
        'receipt_no'
    ];

    /**
     * Date time columns.
     */
    protected $dates = ['date'];

    public function recievedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForReference($query, string $reference){
        return $query->where('general_account_ledgers.reference', $reference);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payer(){
        if($this->model_name == 'Customer')
            return Customer::find($this->model_id);
        if($this->model_name == 'Supplier')
            return Supplier::find($this->model_id);
        if($this->model_name == 'GeneralAccount')
            return GeneralAccount::find($this->model_id);
    }

    public function account(){
        if($this->charged_account_name == 'Customer')
            return Customer::find($this->charged_account_id);
        if($this->charged_account_name == 'Supplier')
            return Supplier::find($this->charged_account_id);
        if($this->charged_account_name == 'GeneralAccount')
            return GeneralAccount::find($this->charged_account_id);
    }

}
