<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'model_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'model_id');
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

    public static function generateNewNumber($prefix = 'RCT', $length = 4){
        $prefix = $prefix.date('ym').auth()->user()->branch->code;
        $record = self::where('receipt_no', 'like', '%'.$prefix.'%')->orderBy('receipt_no', 'desc')->first();
        if($record){
            $number = $record->receipt_no;
            $new = intval(substr($number,strlen($prefix)))+1;
            return $prefix.str_pad($new, $length,0,STR_PAD_LEFT);
        }
        return $prefix.str_pad(1, $length,0,STR_PAD_LEFT);
    }

}
