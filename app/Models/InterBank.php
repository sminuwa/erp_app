<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterBank extends Model
{
    use HasFactory;
    public function transfered_by()
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
    public function source()
    {
        return $this->belongsTo(GeneralAccount::class, 'source_account_id');
    }
    public function destination()
    {
        return $this->belongsTo(GeneralAccount::class, 'destination_account_id');
    }
}
