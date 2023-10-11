<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $reference reference
@property decimal $amount amount
@property bigint $created_by created by
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class Journal extends Model
{

    /**
    * Database table name
    */
    protected $table = 'journals';

    /**
    * Mass assignable columns
    */
    protected $fillable=['reference',
'amount',
'created_by'];

    /**
    * Date time columns.
    */
    protected $dates=[];


    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy(){
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function modifiedBy(){
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function items(){
        return $this->hasMany(JournalItem::class, 'journal_id');
    }

    public static function generateNewNumber($prefix = 'JNL', $length = 4){
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
