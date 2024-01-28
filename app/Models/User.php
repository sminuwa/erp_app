<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

/**
 @property varchar $name name @property varchar $email email @property varchar $phone phone @property enum $gender gender @property varchar $user_code user code @property bigint $branch_id branch id @property tinyint $status status @property timestamp $email_verified_at email verified at @property varchar $password password @property varchar $remember_token remember token @property timestamp $created_at created at @property timestamp $updated_at updated at

 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    const GENDER_MALE = 'Male';
    const GENDER_FEMALE = 'Female';

    /**
     * Database table name
     */
    protected $table = 'users';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['name', 'email', 'phone', 'gender', 'user_code', 'branch_id', 'status'];

    /**
     * Date time columns.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
    public function getUserRole()
    {
        return $this->hasOne(ModelHasRole::class, 'model_id');
    }
    public function getPermisions()
    {
        return $this->hasMany(ModelHasPermission::class, 'model_id');
    }
    public static function userBranchAction()
    {
        return Auth::user()->branch_id ?? 0;
        /*if (Auth::user()->hasAnyRole('Super-admin')) {
         $user_branch = '%';
         }*/
    }
    public static function UserBranchName()
    {
        return Branch::where('id', User::userBranchAction())->first();
    }
    public function totalSales()
    {
        return Order::where('sold_by', $this->id)->sum('total');
    }
    public function todaySale()
    {
        return Order::where('sold_by', $this->id)->whereDate(DB::raw('DATE(order_date)'), '=', date('Y-m-d'))->sum('total');
    }
    //Customers who have not potronize for more than two weeks
    public static function overTwoWeeks()
    {

        $customers = Order::select('customers.*')->distinct()
            ->join('customers', 'customers.id', 'orders.customer_id')
            ->where('type', 'Credit')->where('customers.branch_id', 'LIKE', User::userBranchAction())
            ->having(DB::raw('DATE(MAX(order_date))'), '<', DB::raw('DATE_ADD(DATE(NOW()), INTERVAL -14 DAY)'))->get();
        return $customers;
    }
}
