<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingStaff extends Model
{
    protected $table = 'manufacturing_staff';

    protected $fillable = [
        'employment_id',
        'name',
        'phone_number',
        'address',
        'bvn',
        'nin',
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

    public function teams()
    {
        return $this->belongsToMany(ManufacturingTeam::class, 'manufacturing_team_members', 'staff_id', 'team_id');
    }

    public function teamMemberships()
    {
        return $this->hasMany(ManufacturingTeamMember::class, 'staff_id', 'id');
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
