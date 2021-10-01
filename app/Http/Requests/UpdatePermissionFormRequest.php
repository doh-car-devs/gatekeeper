<?php

namespace App\Http\Requests;

class UpdatePermissionFormRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:permissions,name,'.$this->route('permission'), 'max:255', 'alphanum_special'],
            'description' => ['required', 'max:255', 'alphanum_special'],
        ];
    }
}
