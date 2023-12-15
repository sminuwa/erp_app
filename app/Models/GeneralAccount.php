<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property varchar $class class
@property varchar $number number
@property varchar $description description
@property bigint $branch_id branch id
@property int $is_control is control
@property int $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class GeneralAccount extends Model
{

    /**
     * Database table name
     */
    protected $table = 'general_accounts';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'class',
        'number',
        'description',
        'branch_id',
        'is_control',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function generateNewNumber($prefix, $length = 4){
        $record = self::where('number', 'like', '%'.$prefix.'%')->orderBy('number', 'desc')->first();
        if($record){
            $number = $record->number;
            $new = intval(substr($number,strlen($prefix)))+1;
            return $prefix.str_pad($new, $length,0,STR_PAD_LEFT);
        }
        return $prefix.str_pad(1, $length,0,STR_PAD_LEFT);
    }

    public static function createRecord($class, $description, $is_control = 0, $status = 1, $branch_id = 0){
        $record = new self;
        $record->class = $class;
        $record->number = self::generateNewNumber($class);
        $record->description = $description;
        $record->is_control = $is_control;
        $record->status = $status;
        $record->branch_id = $branch_id;
        if($record->save()){
            return $record;
        }
        return false;
    }

}
