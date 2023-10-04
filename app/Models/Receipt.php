<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
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
}