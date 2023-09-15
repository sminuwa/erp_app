<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bigint $role_id role id
 * @property Role $role belongsTo
 */
class RoleHasPermission extends Model
{

    /**
     * Database table name
     */
    protected $table = 'role_has_permissions';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['role_id',
        'role_id'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }


}
