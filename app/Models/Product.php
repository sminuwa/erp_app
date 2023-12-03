<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
@property varchar $name name @property bigint $category_id category id @property varchar $image image @property tinyint $status status @property int $qty_available qty available @property timestamp $created_at created at @property timestamp $updated_at updated at
*/
class Product extends Model
{

    /**
     * Database table name
     */
    protected $table = 'products';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'name',
        'code',
        'company_id',
        'category_id',
        'barcode',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function transfers()
    {
        return $this->hasMany(TransferProduct::class);
    }
    public function price()
    {
        return $this->hasOne(BranchProductPrice::class, 'product_id', 'id');
    }
    public function storeProducts()
    {
        return $this->hasMany(StoreProduct::class, 'product_id', 'id');
    }
    public function addStoreProduct()
    {
        $stores = Store::get();
        foreach ($stores as $store) {
            $check = StoreProduct::where(['store_id' => $store->id, 'product_id' => $this->id]);
            if ($check != null) {
                StoreProduct::insert(['store_id' => $store->id, 'product_id' => $this->id, 'qty_available' => 0]);
            }
        }

    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public static function product($code)
    {
        return Product::where('code', $code);
    }

    public static function createRecord($company_id, $category_id, $name, $barcode = null, $expiry_status = 0, $status = 1)
    {
        $record = new self;
        $record->code = self::generateNewCode($category_id);
        $record->name = $name;
        $record->company_id = $company_id;
        $record->category_id = $category_id;
        $record->expiry_status = $expiry_status;
        $record->status = $status;
        $record->barcode = $barcode;
        if (is_null($barcode))
            $record->barcode = $record->code;
        if ($record->save())
            return $record;
        return false;
    }

    public static function generateNewCode($category_id, $length = 4)
    {
        $prefix = Category::find($category_id)->code;
        $record = self::where('code', 'like', '%' . $prefix . '%')->orderBy('code', 'desc')->first();
        if ($record) {
            $code = $record->code;
            $new = intval(substr($code, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }

    public function scopeForCategory($query, int $category_id = null)
    {
        if (is_null($category_id))
            return $query;
        return $query->where('category_id', $category_id);
    }
    public function productUnitMeasure()
    {
        return $this->hasMany(ProductUnitMeasure::class, 'product_id');
    }
}
