<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseExpense extends Model
{
    use HasFactory;
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by');
    }

    public function postedBy(){
        return $this->belongsTo(User::class,'posted_by');
    }

    public static function generateNewNumber($prefix = 'PNV', $length = 4){
        $prefix = $prefix.date('ym').auth()->user()->branch->code;
        $record = self::where('reference', 'like', '%'.$prefix.'%')->orderBy('reference', 'desc')->first();
        if($record){
            $number = $record->reference;
            $new = intval(substr($number,strlen($prefix)))+1;
            return $prefix.str_pad($new, $length,0,STR_PAD_LEFT);
        }
        return $prefix.str_pad(1, $length,0,STR_PAD_LEFT);
    }
}
