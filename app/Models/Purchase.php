<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;
/**
 @property bigint $supplier_id supplier id @property varchar $invoice invoice @property date $purchase_date purchase date @property enum $purchase_mode purchase mode @property varchar $address address @property varchar $wbno wbno @property bigint $source_store_id source store id @property bigint $product_id product id @property int $qty_supplied qty supplied @property decimal $unit_price unit price @property bigint $destination_store_id destination store id @property tinyint $status status @property bigint $updated_by updated by @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class Purchase extends Model

{
    const PURCHASE_MODE_CASH = 'Cash';
    const PURCHASE_MODE_CREDIT = 'Credit';
    const PURCHASE_MODE_CASH_CREDIT = 'Cash/Credit';

    /**
     * Database table name
     */
    protected $table = 'purchases';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['supplier_id', 'invoice', 'purchase_date', 'purchase_mode', 'vehicle_reg_no', 'wbno', 'source_store_id', 'product_id', 'qty_supplied', 'unit_price', 'destination_store_id', 'status', 'updated_by'];

    /**
     * Date time columns.
     */
    protected $dates = ['purchase_date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class , 'supplier_id', 'id');
    }
    public function purchasedProducts()
    {
        return $this->hasMany(PurchaseProduct::class);
    }
    public function sourceStore()
    {
        return $this->belongsTo(Store::class , 'source_store_id', 'id');
    }
    public function destinationStore()
    {
        return $this->belongsTo(Store::class , 'destination_store_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class , 'updated_by', 'id');
    }
    public function totalProductCost()
    {
        return $this->purchasedProducts()->select(DB::raw('sum(qty_supplied * unit_price) as total'))->first();
    }
    public function totalPaid()
    {
        if ($this->purchase_mode == 'Cash')
            return SupplierLedger::where('purchase_id', $this->id)->sum('dr'); //>SupplierLedger::where('purchase_id',$this->id)->sum('dr')->first();
        else
            return SupplierLedger::where('purchase_id', $this->id)->sum('cr'); //>SupplierLedger::where('purchase_id',$this->id)->sum('dr')->first();
    }
}