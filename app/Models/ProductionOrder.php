<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductionOrder extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_CLOSED = 'closed';

    protected $table = 'production_orders';

    protected $fillable = [
        'reference',
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
        'approved_by',
        'approved_at',
        'closed_by',
        'closed_at'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'approved_at',
        'closed_at'
    ];

    protected $casts = [
        'total_finish_goods_qty' => 'decimal:4',
        'processed_qty' => 'decimal:4',
    ];

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

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by', 'id');
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

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function canBeEdited()
    {
        return $this->isPending();
    }

    public function canBeApproved()
    {
        return $this->isPending();
    }

    public function canBeClosed()
    {
        return $this->isApproved() && $this->schedules()->count() == 0;
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

    public function getUnproducedQty()
    {
        return $this->total_finish_goods_qty - $this->processed_qty;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    public function scopeForBranch($query, $branch_id = null)
    {
        return is_null($branch_id) ? $query : $query->where('branch_id', $branch_id);
    }
}
