<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    private const REGISTRATION_ROLES = [
        'Bendahara',
        'Kepala Sekolah',
    ];

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'role' => ['required', 'string', 'in:' . implode(',', self::REGISTRATION_ROLES)],
            'keterangan_registrasi' => ['required', 'string', 'max:255'],
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => $input['role'],
            'is_approved' => false,
            'keterangan_registrasi' => $input['keterangan_registrasi'],
        ]);

        // Assign role using Spatie
        Role::findOrCreate($input['role']);
        $user->assignRole($input['role']);

        return $user;
    }
}
