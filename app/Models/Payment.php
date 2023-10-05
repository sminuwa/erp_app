<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    public function received_by()
    {
        return $this->belongsTo(User::class, 'recieved_by');
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
}
