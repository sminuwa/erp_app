<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductionOrder extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_APPROVED = 'approved';
    const STATUS_CLOSED = 'closed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ADJUSTMENT_NEEDED = 'adjustment_needed';

    protected $table = 'production_orders';

    protected $fillable = [
        'reference',
        'order_date',
        'branch_id',
        'start_date',
        'end_date',
        'total_finish_goods_qty',
        'processed_qty',
        'notes',
        'created_by'
    ];

    protected $guarded = [
        'id',
        'status',
        'confirmed_by',
        'confirmed_at',
        'approved_by',
        'approved_at',
        'closed_by',
        'closed_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'adjusted_by',
        'adjusted_at',
        'adjustment_reason',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'confirmed_at',
        'approved_at',
        'closed_at',
        'rejected_at',
        'adjusted_at',
    ];

    protected $casts = [
        'total_finish_goods_qty' => 'decimal:4',
        'processed_qty' => 'decimal:4',
    ];

    // ── Relationships ──────────────────────────────────────

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by', 'id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    public function adjustedBy()
    {
        return $this->belongsTo(User::class, 'adjusted_by', 'id');
    }

    public function items()
    {
        return $this->hasMany(ProductionOrderItem::class, 'production_order_id', 'id');
    }

    public function materials()
    {
        return $this->hasMany(ProductionOrderMaterial::class, 'production_order_id', 'id');
    }

    public function schedules()
    {
        return $this->hasMany(DailyManufacturingSchedule::class, 'production_order_id', 'id');
    }

    public function checklist()
    {
        return $this->hasMany(ProductionOrderChecklist::class, 'production_order_id', 'id');
    }

    // ── Reference Generator ────────────────────────────────

    public static function generateNewNumber($prefix = 'PRO', $length = 4)
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

    // ── Status Checks ──────────────────────────────────────

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isAdjustmentNeeded()
    {
        return $this->status === self::STATUS_ADJUSTMENT_NEEDED;
    }

    // ── Checklist ──────────────────────────────────────────

    public function checklistComplete()
    {
        $total = $this->checklist()->count();
        if ($total === 0) {
            return false;
        }
        return $this->checklist()->where('is_checked', true)->count() === $total;
    }

    // ── Ability Checks ─────────────────────────────────────

    public function canBeEdited()
    {
        return $this->isPending() || $this->isRejected() || $this->isAdjustmentNeeded();
    }

    public function canBeConfirmed()
    {
        return $this->isPending() && $this->checklistComplete();
    }

    public function canBeApproved()
    {
        return $this->isConfirmed();
    }

    public function canBeClosed()
    {
        return $this->isApproved() && $this->schedules()->count() == 0;
    }

    public function canBeRejected()
    {
        return $this->isPending();
    }

    public function canBeAdjusted()
    {
        return $this->isPending();
    }

    public function canBeResubmitted()
    {
        return $this->isRejected() || $this->isAdjustmentNeeded();
    }

    // ── State Transitions ──────────────────────────────────

    public function confirm()
    {
        if (!$this->canBeConfirmed()) {
            return false;
        }

        $this->status = self::STATUS_CONFIRMED;
        $this->confirmed_by = auth()->id();
        $this->confirmed_at = now();
        return $this->save();
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

    public function close()
    {
        if (!$this->canBeClosed()) {
            return false;
        }

        $this->status = self::STATUS_CLOSED;
        $this->closed_by = auth()->id();
        $this->closed_at = now();
        return $this->save();
    }

    public function reject($reason)
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        $this->status = self::STATUS_REJECTED;
        $this->rejected_by = auth()->id();
        $this->rejected_at = now();
        $this->rejection_reason = $reason;
        return $this->save();
    }

    public function adjust($reason)
    {
        if (!$this->canBeAdjusted()) {
            return false;
        }

        $this->status = self::STATUS_ADJUSTMENT_NEEDED;
        $this->adjusted_by = auth()->id();
        $this->adjusted_at = now();
        $this->adjustment_reason = $reason;
        return $this->save();
    }

    public function resubmit()
    {
        if (!$this->canBeResubmitted()) {
            return false;
        }

        $this->status = self::STATUS_PENDING;
        // Clear reject/adjust tracking
        $this->rejected_by = null;
        $this->rejected_at = null;
        $this->rejection_reason = null;
        $this->adjusted_by = null;
        $this->adjusted_at = null;
        $this->adjustment_reason = null;

        // Reset checklist
        $this->checklist()->update([
            'is_checked' => false,
            'checked_by' => null,
            'checked_at' => null,
        ]);

        return $this->save();
    }

    // ── Helpers ─────────────────────────────────────────────

    public function getUnproducedQty()
    {
        return $this->total_finish_goods_qty - $this->processed_qty;
    }

    // ── Scopes ──────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_APPROVED]);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }
}
