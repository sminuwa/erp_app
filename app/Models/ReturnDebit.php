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
        'branch_id',
        'comment',
        'posted_by'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];




}