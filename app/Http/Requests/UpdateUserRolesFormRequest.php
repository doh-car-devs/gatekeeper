<?php

namespace App\Http\Requests;

class UpdateUserRolesFormRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', 'exists:users'],
            'roles' => ['nullable', 'regex:/[0-9,]/'],
        ];
    }
}
