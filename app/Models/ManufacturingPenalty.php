<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingPenalty extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_POSTED = 'posted';

    const PENALTY_TYPE_TEAM = 'team';
    const PENALTY_TYPE_STAFF = 'staff';

    protected $table = 'manufacturing_penalties';

    protected $fillable = [
        'reference',
        'penalty_date',
        'penalty_type',
        'team_id',
        'staff_id',
        'description',
        'amount_charged',
        'total_loss_incurred',
        'production_reference',
        'branch_id',
        'created_by'
    ];

    protected $guarded = ['id', 'status', 'posted_by', 'posted_at'];

    protected $dates = [
        'penalty_date',
        'posted_at'
    ];

    protected $casts = [
        'amount_charged' => 'decimal:2',
        'total_loss_incurred' => 'decimal:2',
    ];

    public function team()
    {
        return $this->belongsTo(ManufacturingTeam::class, 'team_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(ManufacturingStaff::class, 'staff_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }

    public static function generateNewNumber($prefix = 'PEN', $length = 4)
    {
        $prefix = $prefix . date('ym') . auth()->user()->branch->code;
        $record = self::where('reference', 'like', $prefix . '%')->orderBy('reference', 'desc')->first();
        if ($record) {
            $number = $record->reference;
            $new = intval(substr($number, strlen($prefix))) + 1;
            return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
        }
        return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPosted()
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function canBeEdited()
    {
        return $this->isPending();
    }

    public function canBePosted()
    {
        return $this->isPending();
    }

    public function canBeDeleted()
    {
        return $this->isPending();
    }

    public function isTeamPenalty()
    {
        return $this->penalty_type === self::PENALTY_TYPE_TEAM;
    }

    public function isStaffPenalty()
    {
        return $this->penalty_type === self::PENALTY_TYPE_STAFF;
    }

    public function post()
    {
        if (!$this->canBePosted()) {
            return false;
        }

        $this->status = self::STATUS_POSTED;
        $this->posted_by = auth()->id();
        $this->posted_at = now();
        return $this->save();
    }

    public function getPenaltyTarget()
    {
        if ($this->isTeamPenalty()) {
            return $this->team;
        }
        return $this->staff;
    }

    public function getPenaltyTargetName()
    {
        $target = $this->getPenaltyTarget();
        return $target ? $target->name : 'N/A';
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePosted($query)
    {
        return $query->where('status', self::STATUS_POSTED);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }

    public function scopeForTeam($query, $team_id = null)
    {
        return is_null($team_id) ? $query : $query->where('team_id', $team_id);
    }
}
