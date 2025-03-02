<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property varchar $prefix prefix
 * @property varchar $class class
 * @property varchar $description description
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 */
class ChartOfAccount extends Model
{

    /**
     * Database table name
     */
    protected $table = 'chart_of_accounts';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['prefix',
        'class',
        'description'];

    /**
     * Date time columns.
     */
    protected $dates = [];


}
