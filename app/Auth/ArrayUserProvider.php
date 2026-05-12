<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ArrayUserProvider implements UserProvider
{
    protected $users;

    public function __construct()
    {
        $this->users = config('users_list');
    }

    public function retrieveById($identifier)
    {
        $user = collect($this->users)->firstWhere('id', $identifier);
        return $user ? $this->getGenericUser($user) : null;
    }

    public function retrieveByToken($identifier, $token)
    {
        return null; // Kita gak dukung "Remember Me" di mode hardcode
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Do nothing
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return null;
        }

        $user = collect($this->users)->firstWhere('email', $credentials['email']);
        return $user ? $this->getGenericUser($user) : null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // Di mode hardcode kita gak bisa simpan password baru (rehash)
    }

    protected function getGenericUser($userArray)
    {
        $user = new User();
        foreach ($userArray as $key => $value) {
            $user->setAttribute($key, $value);
        }
        $user->id = $userArray['id'];
        $user->exists = true; // Pura-pura data ini ada di database biar Laravel gak komplain
        return $user;
    }
}
