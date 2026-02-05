<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingTeamSupervisor extends Model
{
    protected $table = 'manufacturing_team_supervisors';

    protected $fillable = [
        'team_id',
        'employee_id'
    ];

    public $timestamps = false;

    public function team()
    {
        return $this->belongsTo(ManufacturingTeam::class, 'team_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
