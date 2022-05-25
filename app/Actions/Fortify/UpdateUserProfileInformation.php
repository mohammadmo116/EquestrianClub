<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Admin;
use App\Models\Trainer;
use GuzzleHttp\Psr7\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {         $request=request();


        if($user instanceof Admin){
            Validator::make($input, [

                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class), Rule::unique(Trainer::class), Rule::unique(Admin::class)->ignore($user->id)],
                'phone' => [ 'string', 'max:20'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique(User::class),
                    Rule::unique(Trainer::class),
                    Rule::unique(Admin::class)->ignore($user->id),
                ],

            ])->validateWithBag('updateProfileInformation');
        }
        else if($user instanceof Trainer){
            Validator::make($input, [

                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique(Admin::class),Rule::unique(User::class),Rule::unique(Trainer::class)->ignore($user->id)],
                'phone' => [ 'string', 'max:20'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique(Admin::class),
                    Rule::unique(User::class),
                    Rule::unique(Trainer::class)->ignore($user->id)
                ],

            ])->validateWithBag('updateProfileInformation');
        }
        else if($user instanceof User){
            Validator::make($input, [

                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique(Admin::class),Rule::unique(Trainer::class),Rule::unique(User::class)->ignore($user->id)],
                'phone' => [ 'string', 'max:20'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique(Trainer::class),
                    Rule::unique(Admin::class),
                    Rule::unique(User::class)->ignore($user->id),
                ],

            ])->validateWithBag('updateProfileInformation');
        }


        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'username' => $input['username'],
                'phone' => $input['phone'],

            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'username' => $input['username'],
            'phone' => $input['phone'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
