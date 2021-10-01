<?php

namespace App\Http\Requests;

class CreateRoleFormRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:roles', 'max:255', 'alphanum_special'],
            'description' => ['required', 'max:255', 'alphanum_special'],
            'permissions' => ['nullable', 'regex:/[0-9,]/'],
        ];
    }
}
