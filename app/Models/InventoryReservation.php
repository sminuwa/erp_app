<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryReservation extends Model
{
    const STATUS_RESERVED = 'reserved';
    const STATUS_RELEASED = 'released';
    const STATUS_CONSUMED = 'consumed';

    protected $table = 'inventory_reservations';

    protected $fillable = [
        'reference_type',
        'reference_id',
        'product_id',
        'store_id',
        'reserved_qty',
        'status'
    ];

    protected $casts = [
        'reserved_qty' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function isReserved()
    {
        return $this->status === self::STATUS_RESERVED;
    }

    public function isReleased()
    {
        return $this->status === self::STATUS_RELEASED;
    }

    public function isConsumed()
    {
        return $this->status === self::STATUS_CONSUMED;
    }

    public function release()
    {
        $this->status = self::STATUS_RELEASED;
        return $this->save();
    }

    public function consume()
    {
        $this->status = self::STATUS_CONSUMED;
        return $this->save();
    }

    public function scopeReserved($query)
    {
        return $query->where('status', self::STATUS_RESERVED);
    }

    public function scopeForReference($query, $reference_type, $reference_id)
    {
        return $query->where('reference_type', $reference_type)
            ->where('reference_id', $reference_id);
    }

    public function scopeForProduct($query, $product_id)
    {
        return $query->where('product_id', $product_id);
    }

    public function scopeForStore($query, $store_id)
    {
        return $query->where('store_id', $store_id);
    }

    public static function getTotalReservedQty($product_id, $store_id)
    {
        return self::reserved()
            ->where('product_id', $product_id)
            ->where('store_id', $store_id)
            ->sum('reserved_qty');
    }

    public static function getAvailableQty($product_id, $store_id)
    {
        $storeProduct = StoreProduct::where('product_id', $product_id)
            ->where('store_id', $store_id)
            ->first();

        $available = $storeProduct ? $storeProduct->qty_available : 0;
        $reserved = self::getTotalReservedQty($product_id, $store_id);

        return $available - $reserved;
    }

    public static function createReservation($reference_type, $reference_id, $product_id, $store_id, $qty)
    {
        return self::create([
            'reference_type' => $reference_type,
            'reference_id' => $reference_id,
            'product_id' => $product_id,
            'store_id' => $store_id,
            'reserved_qty' => $qty,
            'status' => self::STATUS_RESERVED
        ]);
    }

    public static function releaseAllForReference($reference_type, $reference_id)
    {
        return self::where('reference_type', $reference_type)
            ->where('reference_id', $reference_id)
            ->update(['status' => self::STATUS_RELEASED]);
    }

    public static function consumeAllForReference($reference_type, $reference_id)
    {
        return self::where('reference_type', $reference_type)
            ->where('reference_id', $reference_id)
            ->update(['status' => self::STATUS_CONSUMED]);
    }
}
