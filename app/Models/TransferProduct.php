<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $source_store_id source store id @property bigint $product_id product id @property bigint $destination_store_id destination store id @property int $qty_transfered qty transfered @property int $qty_available qty available @property bigint $transfered_by transfered by @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class TransferProduct extends Model

{

    /**
     * Database table name
     */
    protected $table = 'transfer_products';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['source_store_id', 'product_id', 'destination_store_id', 'qty_transfered', 'qty_available', 'transfered_by', 'status','transfer_date'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function source()
    {
        return $this->belongsTo(Store::class , 'source_store_id');
    }
    public function destination()
    {
        return $this->belongsTo(Store::class , 'destination_store_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class , 'transfered_by');
    }
    public function transferProducts()
    {
        return $this->where(['transfer_id'=>$this->transfer_id,'stock_in_out'=>'out']);
    }

    public static function generateNewNumber($prefix = 'ITS', $length = 4)
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
