<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proformer extends Model
{
    use HasFactory;
    protected $table = 'proformers';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['customer_id', 'order_date', 'order_status', 'total_products', 'sub_total', 'vat', 'total', 'payment_status', 'pay', 'due'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'sold_by', 'id');
    }
    public function issued()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }
    public function amount()
    {
        return $this->hasMany(CustomerLedger::class);
    }
    public function order_items()
    {
        return $this->hasMany(ProformerDetail::class, 'order_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function generateNewNumber($prefix = 'PRO', $length = 4)
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
