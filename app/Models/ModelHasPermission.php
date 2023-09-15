<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property varchar $model_type model type
 * @property bigint $model_id model id
 */
class ModelHasPermission extends Model
{

    /**
     * Database table name
     */
    protected $table = 'model_has_permissions';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['model_id',
        'model_type',
        'model_id'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function permission()
    {
        return $this->hasOne(Permission::class, 'permission_id');
    }


}
