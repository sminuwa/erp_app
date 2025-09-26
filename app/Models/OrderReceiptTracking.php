<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bigint $id
 * @property bigint $order_id
 * @property bigint $receipt_id
 * @property decimal $applied_amount
 * @property bigint $branch_id
 * @property bigint $applied_by
 * @property timestamp $created_at
 * @property timestamp $updated_at
 */
class OrderReceiptTracking extends Model
{
    /**
     * Database table name
     */
    protected $table = 'order_receipt_trackings';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'order_id',
        'receipt_id',
        'applied_amount',
        'branch_id',
        'applied_by'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * The order that this tracking belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * The receipt that was applied
     */
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    /**
     * The branch where this application was made
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * The user who applied this receipt
     */
    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    /**
     * Get total applied amount for a specific receipt
     */
    public static function getTotalAppliedForReceipt($receipt_id)
    {
        return static::where('receipt_id', $receipt_id)->sum('applied_amount');
    }

    /**
     * Get remaining balance for a receipt
     */
    public static function getRemainingBalance(Receipt $receipt)
    {
        $totalApplied = static::getTotalAppliedForReceipt($receipt->id);
        return $receipt->amount - $totalApplied;
    }

    /**
     * Get all applied receipts for an order
     */
    public static function getOrderReceipts($order_id)
    {
        return static::with('receipt')
            ->where('order_id', $order_id)
            ->get();
    }

    /**
     * Reverse all receipt applications for an order
     */
    public static function reverseOrderApplications($order_id)
    {
        return static::where('order_id', $order_id)->delete();
    }
}