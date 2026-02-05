<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingMachine extends Model
{
    protected $table = 'manufacturing_machines';

    protected $fillable = [
        'code',
        'description',
        'capacity',
        'capacity_unit',
        'branch_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public static function generateNewCode($prefix = 'MCH', $length = 4)
    {
        $record = self::where('code', 'like', $prefix . '%')->orderBy('code', 'desc')->first();
        if ($record) {
            $number = $record->code;
            $new = intval(substr($number, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }
}
