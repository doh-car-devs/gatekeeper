<?php

namespace App\Http\Requests;

class CreatePermissionFormRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:permissions', 'max:255', 'regex:/\w+:\w+/', 'alphanum_special'],
            'description' => ['required', 'max:255', 'alphanum_special'],
        ];
    }
}
