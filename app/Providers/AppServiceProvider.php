<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('alphanum_special', function ($attribute, $value, $parameters, $validator) {
            preg_match_all('/[a-z0-9 .,:\/&#()\-\']/i', $value, $matches);

            return strlen($value) === count($matches[0]);
        }, 'The :attribute format is invalid.');

        Validator::extend('unique_name', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();

            $user = User::query();

            // first param is id value to be ignored
            if ($parameters[0] !== null) {
                $user->where('id', '!=', $parameters[0]);
            }

            // second param is middle name field
            if (empty($parameters[1])) {
                $user->whereNull($parameters[1]);
            } else {
                $user->where($parameters[1], $data[$parameters[1]]);
            }

            // third param is last name field
            $user->where($parameters[2], $data[$parameters[2]]);

            // last param is name suffix field
            if (empty($parameters[3])) {
                $user->whereNull($parameters[3]);
            } else {
                $user->where($parameters[3], $data[$parameters[3]]);
            }

            $user = $user->first();

            return $user === null;
        }, 'User already has an account.');
    }
}
