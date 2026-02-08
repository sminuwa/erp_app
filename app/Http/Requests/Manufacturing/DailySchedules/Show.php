<?php

namespace App\Http\Requests\Manufacturing\DailySchedules;

use Illuminate\Foundation\Http\FormRequest;

class Show extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manufacturing.schedules.show');
    }

    public function rules()
    {
        return [];
    }
}
