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
    protected $fillable = ['name', 'code', 
    'category_id',
     'barcode','status'];

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
    public function dosage()
    {
        return $this->belongsTo(DosageForm::class,'dosage_form_id','id');
    }
}