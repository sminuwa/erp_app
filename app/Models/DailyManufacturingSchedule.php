<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyManufacturingSchedule extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_RECEIVED = 'received';

    protected $table = 'daily_manufacturing_schedules';

    protected $fillable = [
        'reference',
        'schedule_date',
        'production_order_id',
        'branch_id',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'received_by',
        'received_at',
        'created_by'
    ];

    protected $dates = [
        'schedule_date',
        'approved_at',
        'received_at',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(DailyManufacturingScheduleItem::class, 'schedule_id', 'id');
    }

    public function requisitions()
    {
        return $this->hasMany(MaterialsRequisition::class, 'schedule_id', 'id');
    }

    public function workOrders()
    {
        return $this->hasMany(ManufacturingWorkOrder::class, 'daily_schedule_id', 'id');
    }

    public function reservations()
    {
        return InventoryReservation::where('reference_type', 'DailyManufacturingSchedule')
            ->where('reference_id', $this->id);
    }

    public static function generateNewNumber($prefix = 'DMS', $length = 4)
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

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isReceived()
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    public function canBeEdited()
    {
        return $this->isPending();
    }

    public function canBeApproved()
    {
        return $this->isPending();
    }

    public function canBeReceived()
    {
        return $this->isApproved();
    }

    public function approve()
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        $this->approved_by = auth()->id();
        $this->approved_at = now();
        return $this->save();
    }

    public function receive()
    {
        if (!$this->canBeReceived()) {
            return false;
        }

        $this->status = self::STATUS_RECEIVED;
        $this->received_by = auth()->id();
        $this->received_at = now();
        return $this->save();
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', self::STATUS_RECEIVED);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }
}
