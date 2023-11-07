<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property varchar $reference_no reference no
@property bigint $customer_id customer id
@property text $comment comment
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class CreditNote extends Model
{

    /**
     * Database table name
     */
    protected $table = 'credit_notes';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'reference_no',
        'customer_id',
        'comment'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class,'posted_by');
    }

    public static function generateNewNumber($prefix = 'CRN', $length = 4)
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

}
