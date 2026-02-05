<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingTeam extends Model
{
    protected $table = 'manufacturing_teams';

    protected $fillable = [
        'name',
        'branch_id',
        'ledger_balance',
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

    public function supervisors()
    {
        return $this->hasMany(ManufacturingTeamSupervisor::class, 'team_id', 'id');
    }

    public function members()
    {
        return $this->hasMany(ManufacturingTeamMember::class, 'team_id', 'id');
    }

    public function supervisorEmployees()
    {
        return $this->belongsToMany(Employee::class, 'manufacturing_team_supervisors', 'team_id', 'employee_id');
    }

    public function memberStaff()
    {
        return $this->belongsToMany(ManufacturingStaff::class, 'manufacturing_team_members', 'team_id', 'staff_id');
    }

    public function singleManufacturing()
    {
        return $this->hasMany(SingleProductManufacturing::class, 'team_id', 'id');
    }

    public function batchProductions()
    {
        return $this->hasMany(BatchProduction::class, 'team_id', 'id');
    }

    public function penalties()
    {
        return $this->hasMany(ManufacturingPenalty::class, 'team_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }

    public function addPenalty($amount)
    {
        $this->ledger_balance += $amount;
        return $this->save();
    }
}
