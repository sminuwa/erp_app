<?php

namespace App\Http\Requests\Manufacturing\DailySchedules;

use Illuminate\Foundation\Http\FormRequest;

class Approve extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.schedules.approve');
    }

    public function rules()
    {
        return [];
    }
}
