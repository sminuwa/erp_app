<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $store_id store id @property bigint $product_id product id @property int $available_qty available qty @property int $adjusted_qty adjusted qty @property date $date date @property varchar $refno refno @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class StockAdjustment extends Model

{

    /**
     * Database table name
     */
    protected $table = 'stock_adjustments';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['store_id', 'product_id', 'available_qty', 'adjusted_qty', 'date', 'refno'];

    /**
     * Date time columns.
     */
    protected $dates = ['date'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class , 'adjusted_by', 'id');
    }

    public function adjustedProducts()
    {
        return $this->where(['refno'=>$this->refno]);
    }

    public static function generateNewNumber($prefix = 'STK', $length = 4)
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

    public function products(){
        return $this->hasMany(StockAdjustmentDetail::class);
    }
}
