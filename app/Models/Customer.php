<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Builder\Function_;

/**
 @property varchar $name name @property varchar $email email @property varchar $phone phone @property varchar $address address @property varchar $photo photo @property decimal $opening_balance opening balance @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class Customer extends Model
{

    /**
     * Database table name
     */
    protected $table = 'customers';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'name',
        'code',
        'email',
        'branch_id',
        'phone',
        'address',
        'type',
        'credit_limit',
        'gender',
        'business_type',
        'business_age',
        'relation_officer'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function ledgers()
    {
        return $this->hasMany(GeneralAccountLedger::class, 'model_id');
    }
    public function amount()
    {
        return $this->hasMany(CustomerLedger::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function runningBalance()
    {
        return $this->ledgers()->where('model_name', 'Customer')->sum('credit') - $this->ledgers()->where('model_name', 'Customer')->sum('debit');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
    public function relationOfficer()
    {
        return $this->belongsTo(User::class, 'relation_officer');
    }

    public function scopeActive($query)
    {
        return $query->where('customers.status', 1);
    }
}
