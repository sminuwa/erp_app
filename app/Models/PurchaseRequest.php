<?php
namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

/**
   @property bigint $supplier_id supplier id
@property varchar $invoice invoice
@property datetime $purchase_date purchase date
@property enum $purchase_mode purchase mode
@property varchar $wbno wbno
@property bigint $source_store_id source store id
@property bigint $destination_store_id destination store id
@property tinyint $status status
@property varchar $waybill_no waybill no
@property varchar $driver_name driver name
@property varchar $location_id location id
@property varchar $warehouse warehouse
@property varchar $vehicle_reg_no vehicle reg no
@property varchar $transporter transporter
@property bigint $updated_by updated by
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class PurchaseRequest extends Model
{
    const PURCHASE_MODE_CASH = 'Cash';

    const PURCHASE_MODE_CREDIT = 'Credit';

    const PURCHASE_MODE_CASH_CREDIT = 'Cash/Credit';

    /**
     * Database table name
     */
    protected $table = 'purchase_requests';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'supplier_id',
        'invoice',
        'purchase_date',
        'purchase_mode',
        'wbno',
        'source_store_id',
        'destination_store_id',
        'status',
        'waybill_no',
        'driver_name',
        'location_id',
        'warehouse',
        'vehicle_reg_no',
        'transporter',
        'updated_by'
    ];

    /**
     * Date time columns.
     */
    protected $dates = ['purchase_date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function purchasedProducts()
    {
        return $this->hasMany(PurchaseProductRequest::class,'purchase_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
    public function totalProductCost()
    {
        return $this->purchasedProducts()->select(DB::raw('sum(quantity * unit_price) as total'))->first();
    }
    public function totalPaid()
    {
        if ($this->purchase_mode == 'Cash')
            return SupplierLedger::where('purchase_id', $this->id)->sum('dr'); //>SupplierLedger::where('purchase_id',$this->id)->sum('dr')->first();
        else
            return SupplierLedger::where('purchase_id', $this->id)->sum('cr'); //>SupplierLedger::where('purchase_id',$this->id)->sum('dr')->first();
    }
    public function expenses()
    {
        return $this->hasMany(PurchaseExpense::class,'purchase_id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by');
    }

    public static function generateNewNumber($prefix = 'PUR', $length = 4){
        $prefix = $prefix.date('ym').auth()->user()->branch->code;
        $record = self::where('reference', 'like', '%'.$prefix.'%')->orderBy('reference', 'desc')->first();
        if($record){
            $number = $record->reference;
            $new = intval(substr($number,strlen($prefix)))+1;
            return $prefix.str_pad($new, $length,0,STR_PAD_LEFT);
        }
        return $prefix.str_pad(1, $length,0,STR_PAD_LEFT);
    }


}
