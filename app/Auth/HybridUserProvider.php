<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Auth\EloquentUserProvider;

class HybridUserProvider implements UserProvider
{
    protected $eloquentProvider;
    protected $arrayProvider;

    public function __construct($app)
    {
        // Siapkan provider database bawaan Laravel
        $this->eloquentProvider = new EloquentUserProvider($app['hash'], \App\Models\User::class);
        // Siapkan provider hardcode buatan kita
        $this->arrayProvider = new ArrayUserProvider();
    }

    /**
     * Fungsi buat ngecek saklar (dengan fitur Ingatan/Cache)
     */
    protected function getSource()
    {
        return Cache::remember('auth_source', 3600, function () {
            try {
                $setting = DB::table('settings')->where('key', 'auth_source')->first();
                return $setting ? $setting->value : 'hardcode'; // Fallback kalau datanya gak ada
            } catch (\Exception $e) {
                return 'hardcode'; // Fallback kalau databasenya mati
            }
        });
    }

    public function retrieveById($identifier)
    {
        return $this->getSource() === 'database' 
            ? $this->eloquentProvider->retrieveById($identifier)
            : $this->arrayProvider->retrieveById($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        return $this->getSource() === 'database' 
            ? $this->eloquentProvider->retrieveByToken($identifier, $token)
            : $this->arrayProvider->retrieveByToken($identifier, $token);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        if ($this->getSource() === 'database') {
            $this->eloquentProvider->updateRememberToken($user, $token);
        }
    }

    public function retrieveByCredentials(array $credentials)
    {
        return $this->getSource() === 'database' 
            ? $this->eloquentProvider->retrieveByCredentials($credentials)
            : $this->arrayProvider->retrieveByCredentials($credentials);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->getSource() === 'database' 
            ? $this->eloquentProvider->validateCredentials($user, $credentials)
            : $this->arrayProvider->validateCredentials($user, $credentials);
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        if ($this->getSource() === 'database') {
            $this->eloquentProvider->rehashPasswordIfRequired($user, $credentials, $force);
        }
    }
}
