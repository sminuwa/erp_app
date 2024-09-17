<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property varchar $reference_no reference no
@property varchar $invoice_no invoice no
@property bigint $customer_id customer id
@property decimal $amount amount
@property bigint $branch_id branch id
@property text $comment comment
@property bigint $posted_by posted by
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class ReturnDebit extends Model
{

    /**
     * Database table name
     */
    protected $table = 'return_debits';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'reference_no',
        'invoice_no',
        'customer_id',
        'amount',
        'date',
        'branch_id',
        'comment',
        'posted_by'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
    protected $dates = [];

    public function returnItems()
    {
        return $this->hasMany(ReturnDebitItem::class);
    }

    public function products()
    {
        return $this->hasMany(ReturnDebitItem::class);
    }
    public static function generateNewNumber($prefix = 'RAD', $length = 4)
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
