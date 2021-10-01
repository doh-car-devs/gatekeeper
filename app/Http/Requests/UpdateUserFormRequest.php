<?php

namespace App\Http\Requests;

class UpdateUserFormRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'given_name' => ['required', 'max:255', 'unique:users,given_name,'
                .$this->route('id')
                .',id,middle_name,'.$this->input('middle_name')
                .',last_name,'.$this->input('last_name')
                .',name_suffix,'.$this->input('name_suffix'),
            ],
            'middle_name' => ['nullable', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'name_suffix' => ['nullable', 'max:255'],
            'office_id' => ['required', 'exists:offices,id'],
            'username' => ['required', 'max:255', 'unique:users,username,'.$this->route('user')],
            'password' => ['nullable', 'min:8', 'max:255', 'regex:/^[a-z0-9`~!@#$%^&-_=+\[{\]}.]+$/i'],
            'confirm_password' => ['nullable', 'same:password'],
            'roles' => ['nullable', 'regex:/[0-9,]/'],
        ];
    }
}
