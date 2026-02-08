<?php

namespace App\Http\Requests\Manufacturing\DailySchedules;

use Illuminate\Foundation\Http\FormRequest;

class Destroy extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.schedules.delete');
    }

    public function rules()
    {
        return [];
    }
}
