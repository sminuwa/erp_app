<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property varchar $name name @property varchar $phone phone @property varchar $email email @property varchar $address address @property tinyint $status status @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class Branch extends Model
{

    /**
     * Database table name
     */
    protected $table = 'branches';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['company_id', 'name', 'code', 'status'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function stores()
    {
        return $this->hasMany(Store::class, 'branch_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function scopeForCompany($query, int $company_id = null)
    {
        if (is_null($company_id))
            return $query;
        return $query->where('company_id', $company_id);
    }

}
