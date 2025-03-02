<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $branch_id branch id
@property date $date date
@property bigint $created_by created by
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class InterstoreTransfer extends Model
{

    /**
    * Database table name
    */
    protected $table = 'interstore_transfers';

    /**
    * Mass assignable columns
    */
    protected $fillable=['branch_id',
'date',
'created_by'];

    /**
    * Date time columns.
    */
    protected $dates=['date'];


    public function products(){
        return $this->hasMany(InterstoreTransferDetail::class ,'interstore_transfer_id');
    }

    public static function generateNewNumber($prefix = 'ITS', $length = 4)
    {
        $prefix = $prefix . date('ym') . auth()->user()->branch->code;
        $record = self::where('reference', 'like', '%' . $prefix . '%')->orderBy('reference', 'desc')->first();
        if ($record) {
            $number = $record->reference;
            $new = intval(substr($number, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }


    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }


}
