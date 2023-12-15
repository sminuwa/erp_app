<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property bigint $intersite_transfer_id intersite transfer id
@property bigint $product_id product id
@property bigint $cost_price cost price
@property int $quantity_requested quantity requested
@property int $quantity_approved quantity approved
@property bigint $source_store_id source store id
@property bigint $destination_store_id destination store id
@property tinyint $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property DestinationStore $store belongsTo
@property SourceStore $store belongsTo
@property Product $product belongsTo
@property IntersiteTransfer $intersiteTransfer belongsTo

 */
class IntersiteTransferProduct extends Model
{

    /**
     * Database table name
     */
    protected $table = 'intersite_transfer_products';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'intersite_transfer_id',
        'product_id',
        'cost_price',
        'quantity_requested',
        'quantity_approved',
        'source_store_id',
        'destination_store_id',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * destinationStore
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function destinationStore()
    {
        return $this->belongsTo(Store::class, 'destination_store_id');
    }

    /**
     * sourceStore
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sourceStore()
    {
        return $this->belongsTo(Store::class, 'source_store_id');
    }

    /**
     * product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * intersiteTransfer
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function intersiteTransfer()
    {
        return $this->belongsTo(IntersiteTransfer::class, 'intersite_transfer_id');
    }

    public function intersite()
    {
        return $this->belongsTo(IntersiteTransfer::class, 'intersite_transfer_id');
    }

    public function totalReceive(){
        $products = IntersiteTransferReceive::where(['intersite_transfer_id'=> $this->intersite_transfer_id, 'product_id'=>$this->product_id, 'source_store_id'=>$this->store_id])->first();
        return ($products->quantity ?? 0);
    }


}
