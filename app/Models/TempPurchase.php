<?php
namespace App\Models;

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
class TempPurchase extends Model 
{
    const PURCHASE_MODE_CASH='Cash';

const PURCHASE_MODE_CREDIT='Credit';

const PURCHASE_MODE_CASH_CREDIT='Cash/Credit';

    /**
    * Database table name
    */
    protected $table = 'temp_purchases';

    /**
    * Mass assignable columns
    */
    protected $fillable=['supplier_id',
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
'updated_by'];

    /**
    * Date time columns.
    */
    protected $dates=['purchase_date'];




}