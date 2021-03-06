<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanHorarioRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'hora_desde' => 'required',
            'hora_hasta' => 'required',
            'duracion' => 'required',
            'dias' => 'required',
        ];
    }
}
