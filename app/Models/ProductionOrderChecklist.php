<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionOrderChecklist extends Model
{
    protected $table = 'production_order_checklists';

    protected $fillable = [
        'production_order_id',
        'category',
        'label',
        'is_checked',
        'checked_by',
        'checked_at',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
    ];

    protected $dates = [
        'checked_at',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id', 'id');
    }

    public function checkedBy()
    {
        return $this->belongsTo(User::class, 'checked_by', 'id');
    }
}
