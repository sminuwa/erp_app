<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetExpenditure extends Model
{
    protected $fillable = ['branch_id', 'general_account_id', 'budget_year', 'proposed_budget'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function account()
    {
        return $this->belongsTo(GeneralAccount::class, 'general_account_id');
    }
}
