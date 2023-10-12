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

    public static function generateNewNumber($prefix = 'ITB', $length = 4){
        $prefix = $prefix.date('ymd');
        $record = self::where('receipt_no', 'like', '%'.$prefix.'%')->orderBy('receipt_no', 'desc')->first();
        if($record){
            $number = $record->receipt_no;
            $new = intval(substr($number,strlen($prefix)))+1;
            return $prefix.str_pad($new, $length,0,STR_PAD_LEFT);
        }
        return $prefix.str_pad(1, $length,0,STR_PAD_LEFT);
    }

    public function source_account(){
            return GeneralAccount::find($this->source_account_id);
    }

    public function destination_account(){
            return GeneralAccount::find($this->destination_account_id);
    }

}
