<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MaterialsRequisition extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_ISSUED = 'issued';
    const STATUS_VERIFIED = 'verified';
    const STATUS_RECEIVED = 'received';

    protected $table = 'materials_requisitions';

    protected $fillable = [
        'reference',
        'requisition_date',
        'schedule_id',
        'work_order_id',
        'bom_id',
        'quantity',
        'branch_id',
        'created_by',
        'notes'
    ];

    protected $guarded = [
        'id',
        'status',
        'approved_by',
        'approved_at',
        'issued_by',
        'issued_at',
        'verified_by',
        'verified_at',
        'received_by',
        'received_at'
    ];

    protected $dates = [
        'requisition_date',
        'approved_at',
        'issued_at',
        'verified_at',
        'received_at'
    ];

    public function schedule()
    {
        return $this->belongsTo(DailyManufacturingSchedule::class, 'schedule_id', 'id');
    }

    public function workOrder()
    {
        return $this->belongsTo(ManufacturingWorkOrder::class, 'work_order_id', 'id');
    }

    public function bom()
    {
        return $this->belongsTo(ManufacturingBom::class, 'bom_id', 'id');
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

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by', 'id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(MaterialsRequisitionItem::class, 'requisition_id', 'id');
    }

    public function singleProductManufacturing()
    {
        return $this->hasMany(SingleProductManufacturing::class, 'requisition_id', 'id');
    }

    public function batchProductions()
    {
        return $this->hasMany(BatchProduction::class, 'requisition_id', 'id');
    }

    public function reservations()
    {
        return InventoryReservation::where('reference_type', 'MaterialsRequisition')
            ->where('reference_id', $this->id);
    }

    public static function generateNewNumber($prefix = 'MRF', $length = 4)
    {
        return DB::transaction(function () use ($prefix, $length) {
            $prefix = $prefix . date('ym') . auth()->user()->branch->code;

            // Use lockForUpdate() to prevent race conditions
            $record = self::where('reference', 'like', $prefix . '%')
                ->orderBy('reference', 'desc')
                ->lockForUpdate()
                ->first();

            if ($record) {
                $number = $record->reference;
                $new = intval(substr($number, strlen($prefix))) + 1;
                return $prefix . str_pad($new, $length, 0, STR_PAD_LEFT);
            }
            return $prefix . str_pad(1, $length, 0, STR_PAD_LEFT);
        });
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isIssued()
    {
        return $this->status === self::STATUS_ISSUED;
    }

    public function isVerified()
    {
        return $this->status === self::STATUS_VERIFIED;
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

    public function canBeIssued()
    {
        return $this->isApproved();
    }

    public function canBeVerified()
    {
        return $this->isIssued();
    }

    public function canBeReceived()
    {
        return $this->isVerified();
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

    public function issue()
    {
        if (!$this->canBeIssued()) {
            return false;
        }

        $this->status = self::STATUS_ISSUED;
        $this->issued_by = auth()->id();
        $this->issued_at = now();
        return $this->save();
    }

    public function verify()
    {
        if (!$this->canBeVerified()) {
            return false;
        }

        $this->status = self::STATUS_VERIFIED;
        $this->verified_by = auth()->id();
        $this->verified_at = now();
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

    public function isLinkedToSchedule()
    {
        return !is_null($this->schedule_id);
    }

    public function isLinkedToWorkOrder()
    {
        return !is_null($this->work_order_id);
    }

    public function isLinkedToBom()
    {
        return !is_null($this->bom_id);
    }

    public function getBomType()
    {
        if ($this->isLinkedToBom()) {
            return $this->bom->bom_type;
        }

        if ($this->isLinkedToWorkOrder()) {
            $firstItem = $this->workOrder->items()->first();
            if ($firstItem && $firstItem->scheduleItem && $firstItem->scheduleItem->productionOrderItem && $firstItem->scheduleItem->productionOrderItem->bom) {
                return $firstItem->scheduleItem->productionOrderItem->bom->bom_type;
            }
        }

        if ($this->isLinkedToSchedule()) {
            $firstItem = $this->schedule->items()->first();
            if ($firstItem && $firstItem->productionOrderItem && $firstItem->productionOrderItem->bom) {
                return $firstItem->productionOrderItem->bom->bom_type;
            }
        }

        return null;
    }

    public function isSingleBom()
    {
        return $this->getBomType() === ManufacturingBom::TYPE_SINGLE;
    }

    public function isBatchBom()
    {
        return $this->getBomType() === ManufacturingBom::TYPE_BATCH;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', self::STATUS_VERIFIED);
    }

    public function scopeReceived($query)
    {
        return $query->where('status', self::STATUS_RECEIVED);
    }

    public function scopeAvailableForManufacturing($query)
    {
        return $query->whereIn('status', [self::STATUS_VERIFIED, self::STATUS_RECEIVED]);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }

    public function scopeWithSingleBom($query)
    {
        return $query->whereHas('bom', function ($q) {
            $q->where('bom_type', ManufacturingBom::TYPE_SINGLE);
        });
    }

    public function scopeWithBatchBom($query)
    {
        return $query->whereHas('bom', function ($q) {
            $q->where('bom_type', ManufacturingBom::TYPE_BATCH);
        });
    }
}
