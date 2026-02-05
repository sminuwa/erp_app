<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingTeamMember extends Model
{
    protected $table = 'manufacturing_team_members';

    protected $fillable = [
        'team_id',
        'staff_id'
    ];

    public $timestamps = false;

    public function team()
    {
        return $this->belongsTo(ManufacturingTeam::class, 'team_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(ManufacturingStaff::class, 'staff_id', 'id');
    }
}
