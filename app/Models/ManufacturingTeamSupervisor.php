<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingTeamSupervisor extends Model
{
    protected $table = 'manufacturing_team_supervisors';

    protected $fillable = [
        'team_id',
        'user_id'
    ];

    public $timestamps = false;

    public function team()
    {
        return $this->belongsTo(ManufacturingTeam::class, 'team_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
