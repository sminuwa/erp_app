<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property bigint $journal_id journal id
 * @property bigint $account_id account id
 * @property varchar $account_type account type
 * @property decimal $credit credit
 * @property decimal $debit debit
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 */
class JournalItem extends Model
{

    /**
     * Database table name
     */
    protected $table = 'journal_items';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['journal_id',
        'account_id',
        'account_type',
        'credit',
        'debit',
        'description'];

    /**
     * Date time columns.
     */
    protected $dates = [];


}
