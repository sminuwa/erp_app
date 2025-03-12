<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property int $staff_id Staff Id
@property int $branch_id Branch Id
@property int $category_id Category Id
@property year $budget_year Budget Year
@property string $quarter Quarter
@property int $month1 Month1
@property int $month2 Month2
@property int $month3 Month3
@property int $total Total
@property \Carbon\Carbon $created_at Created At
@property \Carbon\Carbon $updated_at Updated At
@property User $user BelongsTo
@property Branch $branch BelongsTo
@property Category $category BelongsTo
   
 */
class RoBudget extends Model 
{
    	 const QUARTER_Q1='Q1';
	 const QUARTER_Q2='Q2';
	 const QUARTER_Q3='Q3';
	 const QUARTER_Q4='Q4';
    /**
    * Database table name
    */
    protected $table = 'ro_budgets';

    /**
    * Mass assignable columns
    */
    protected $fillable=[		'staff_id',
		'branch_id',
		'category_id',
		'budget_year',
		'quarter',
		'month1',
		'month2',
		'month3',
		'total'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    /**
    * user
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function user()
    {
        return $this->belongsTo(User::class,'staff_id','id');
    }

    /**
    * branch
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function branch()
    {
        return $this->belongsTo(Branch::class,'branch_id','id');
    }

    /**
    * category
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }




}