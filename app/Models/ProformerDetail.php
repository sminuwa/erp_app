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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function order()
    {
        return $this->belongsTo(Proformer::class);
    }
}
