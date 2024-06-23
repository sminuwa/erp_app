<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property varchar $invoice invoice
@property bigint $source_branch_id source branch id
@property bigint $destination_branch_id destination branch id
@property bigint $requested_by requested by
@property date $date_requested date requested
@property bigint $approved_by approved by
@property bigint $dated_approved dated approved
@property enum $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property DestinationBranch $branch belongsTo
@property SourceBranch $branch belongsTo

 */
class IntersiteTransfer extends Model
{
    const STATUS_PENDING = 'Pending';

    const STATUS_APPROVED = 'Approved';

    const STATUS_COMPLETED = 'Completed';

    const STATUS_CANCELLED = 'Cancelled';

    /**
     * Database table name
     */
    protected $table = 'intersite_transfers';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'reference_no',
        'source_branch_id',
        'destination_branch_id',
        'requested_by',
        'date_requested',
        'approved_by',
        'dated_approved',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = ['date_requested'];

    /**
     * destinationBranch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destinationBranch()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function destination()
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function sourceBranch()
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }

    public function source()
    {
        return $this->belongsTo(Branch::class, 'source_branch_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function requestProducts()
    {
        return $this->hasMany(IntersiteTransferProduct::class);
    }

    public function products()
    {
        return $this->hasMany(IntersiteTransferProduct::class);
    }

    public function is_completed() : bool{
        $total = 0;
        if(IntersiteTransferProduct::where('intersite_transfer_id', $this->id)->sum('quantity') == IntersiteTransferReceive::where('intersite_transfer_id', $this->id)->sum('quantity'))
            return true;
            /*foreach($this->products as $product){
                if(($product->quantity - $product->totalReceive()) == 0)
                    continue;
                return false;
            }*/
        return false;

    }

    public function receivedProducts()
    {
        return $this->hasMany(IntersiteTransferReceive::class);
    }

    public static function generateNewNumber($prefix = 'ITB', $length = 4)
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

    public function scopePending($query){
        return $query->where('intersite_transfers.status', 0);
    }

    public function scopePosted($query){
        return $query->where('intersite_transfers.status', 1);
    }


    public function scopeReceived($query){
        return $query->where('intersite_transfers.status', 3);
    }

    public function scopeNotPending($query){
        $query->where('intersite_transfers.status', '!=', 0);
    }
}
