<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformerDetail extends Model
{
    use HasFactory;
    protected $table = 'proformer_details';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_cost', 'total'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function storeProduct()
    {
        return $this->belongsTo(StoreProduct::class);
    }
    public function product()
    {
        return $this->storeProduct()->product();
    }
    public function order()
    {
        return $this->belongsTo(Proformer::class);
    }
    public function branch()
    {
        return $this->storeProduct()->select('branches.*')->join('stores','stores.id','store_products.store_id')->join('branches','branches.id','stores.branch_id');
    }
}
