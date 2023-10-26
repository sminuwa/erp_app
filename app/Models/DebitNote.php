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
        'reference_no',
        'invoice_no',
        'supplier_id',
        'amount',
        'branch_id',
        'comment',
        'posted_by'
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


}