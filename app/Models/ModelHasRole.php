<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property varchar $model_type model type
 * @property bigint $model_id model id
 */
class ModelHasRole extends Model
{

    /**
     * Database table name
     */
    protected $table = 'model_has_roles';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['role_id',
        'model_type',
        'model_id'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function role(){
        return $this->belongsTo(Role::class);
    }

}
