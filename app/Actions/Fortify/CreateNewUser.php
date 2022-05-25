<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(Admin::class),
                Rule::unique(Trainer::class),
                Rule::unique(User::class),
            ],
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class), Rule::unique(Trainer::class),Rule::unique(Admin::class)],
            'phone' => [ 'string', 'max:20'],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'username' => $input['username'],
            'phone' => $input['phone'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
