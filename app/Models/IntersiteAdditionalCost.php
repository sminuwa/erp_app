<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IntersiteAdditionalCost extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_POSTED = 'posted';
    const STATUS_REVERSED = 'reversed';

    protected $table = 'intersite_additional_costs';

    protected $fillable = [
        'reference',
        'intersite_transfer_id',
        'supplier_id',
        'cost_mode',
        'amount',
        'date',
        'truck_number',
        'waybill_no',
        'description',
        'branch_id',
        'created_by',
    ];

    protected $guarded = [
        'id',
        'status',
        'posted_by',
        'posted_at',
    ];

    protected $dates = [
        'date',
        'posted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function intersiteTransfer()
    {
        return $this->belongsTo(IntersiteTransfer::class, 'intersite_transfer_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
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

    public function items()
    {
        return $this->hasMany(IntersiteAdditionalCostItem::class, 'intersite_additional_cost_id', 'id');
    }

    public static function generateNewNumber($prefix = 'IAC', $length = 4)
    {
        return DB::transaction(function () use ($prefix, $length) {
            $prefix = $prefix . date('ym') . auth()->user()->branch->code;

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

    public function isPosted()
    {
        return $this->status === self::STATUS_POSTED;
    }

    public function isReversed()
    {
        return $this->status === self::STATUS_REVERSED;
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

    public function canBeReversed()
    {
        if (!$this->isPosted()) {
            return false;
        }
        // Cannot reverse if intersite has already been received/allocated
        return $this->intersiteTransfer && $this->intersiteTransfer->status == 1;
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
}
