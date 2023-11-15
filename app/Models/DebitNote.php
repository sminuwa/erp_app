<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property varchar $reference_no reference no
@property varchar $invoice_no invoice no
@property bigint $supplier_id supplier id
@property decimal $amount amount
@property bigint $branch_id branch id
@property text $comment comment
@property bigint $posted_by posted by
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property Branch $branch belongsTo
@property Supplier $supplier belongsTo

 */
class DebitNote extends Model
{

    /**
     * Database table name
     */
    protected $table = 'debit_notes';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'reference',
        'invoice_no',
        'supplier_id',
        'amount',
        'branch_id',
        'comment',
        'created_by',
        'posted_by',
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * supplier
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateNewNumber($prefix = 'DBN', $length = 4)
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
