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

    public function postedBy()
    {
        return $this->belongsTo(User::class,'posted_by');
    }

}