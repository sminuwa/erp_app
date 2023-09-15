<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierLedger extends Model
{
    protected $table = 'supplier_ledgers';
    protected $fillable = ['supplier_id',
        'description',
        'Ref',
        'cr',
        'dr',
        'date'];
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id','id');
    }
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class , 'bank_account_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
