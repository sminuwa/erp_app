<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
/**
 @property bigint $user_id user id @property varchar $action action @property bigint $role_id role id @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class AuditLog extends Model

{

    /**
     * Database table name
     */
    protected $table = 'audit_logs';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['user_id', 'action', 'role_id'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public static function auditLog($user_id, $action)
    {
        $roles = "";
        foreach (Auth::user()->getRoleNames() as $v) {
            $roles .= $v . ",";
        }

        $audit = new AuditLog();
        $audit->user_id = $user_id;
        $audit->roles = rtrim($roles, ',');
        $audit->action = $action;
        $audit->created_at = Carbon::now();
        $audit->updated_at = Carbon::now();
        $audit->save();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}