<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SupplierLedger;

/**
   @property varchar $name name
@property varchar $phone phone
@property varchar $code code
@property varchar $email email
@property varchar $address address
@property varchar $account_holder account holder
@property varchar $account_number account number
@property enum $account_type account type
@property bigint $bank_id bank id
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class Supplier extends Model
{
    const ACCOUNT_TYPE_CURRENT = 'Current';

    const ACCOUNT_TYPE_SAVINGS = 'Savings';

    /**
     * Database table name
     */
    protected $table = 'suppliers';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'name',
        'phone',
        'code',
        'email',
        'address',
        'account_holder',
        'account_number',
        'account_type',
        'status',
        'bank_id'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];


    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function supplierLedgers()
    {
        return $this->hasMany(SupplierLedger::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function scopeActive($query)
    {
        return $query->where('suppliers.status', 1);
    }

    public static function generateNewCode($branch_id, $category, $length = 6)
    {
        $prefix = Branch::find($branch_id)->code . $category;
        $record = self::where('code', 'like', '%' . $prefix . '%')->orderBy('code', 'desc')->first();
        if ($record) {
            $code = $record->code;
            $new = intval(substr($code, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }
}