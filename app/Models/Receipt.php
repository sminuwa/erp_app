<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_no', 'amount', 'remaining_balance', 'date', 'description',
        'model_id', 'model_name', 'charged_account_id', 'charged_account_name',
        'branch_id', 'status', 'created_by', 'posted_by'
    ];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }
    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'model_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'model_id');
    }

    public function payer()
    {
        if ($this->model_name == 'Customer')
            return Customer::find($this->model_id);
        if ($this->model_name == 'Supplier')
            return Supplier::find($this->model_id);
        if ($this->model_name == 'GeneralAccount')
            return GeneralAccount::find($this->model_id);
    }

    public function account()
    {
        if ($this->charged_account_name == 'Customer')
            return Customer::find($this->charged_account_id);
        if ($this->charged_account_name == 'Supplier')
            return Supplier::find($this->charged_account_id);
        if ($this->charged_account_name == 'GeneralAccount')
            return GeneralAccount::find($this->charged_account_id);
    }

    public static function generateNewNumber($date, $prefix = 'RCT', $length = 4)
    {
        $prefix = $prefix . $date . auth()->user()->branch->code;
        $record = self::where('receipt_no', 'like', '%' . $prefix . '%')->orderBy('receipt_no', 'desc')->first();
        if ($record) {
            $number = $record->receipt_no;
            $new = intval(substr($number, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }

    /**
     * Get the order receipt trackings for this receipt
     */
    public function orderReceiptTrackings()
    {
        return $this->hasMany(OrderReceiptTracking::class);
    }

    /**
     * Get the total applied amount for this receipt
     */
    public function getTotalAppliedAmount()
    {
        return $this->orderReceiptTrackings()->sum('applied_amount');
    }

    /**
     * Update the remaining balance based on applied amounts
     */
    public function updateRemainingBalance()
    {
        $totalApplied = $this->getTotalAppliedAmount();
        $this->remaining_balance = $this->amount - $totalApplied;
        $this->save();
        return $this->remaining_balance;
    }

    /**
     * Check if receipt has available balance
     */
    public function hasAvailableBalance()
    {
        return $this->remaining_balance > 0;
    }

    /**
     * Get customer receipts with available balance for POS
     */
    public static function getAvailableCustomerReceipts($customer_id, $limit = 50)
    {
        return self::where('model_name', 'Customer')
            ->where('model_id', $customer_id)
            ->where('status', 1) // Only posted receipts
            ->where('remaining_balance', '>', 0)
            ->orderBy('date', 'asc') // Oldest first for better optimization
            ->take($limit)
            ->get();
    }

}
