<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Models\User;

class SocialGoogleAccountService
{
    /**
     * create or get user from google account service
     *
     * @param Laravel\Socialite\Contracts\User $providerUser
     * @return \App\Models\User
     */
    public static function createOrGetUser(ProviderUser $providerUser)
    {
        $email = $providerUser->getEmail();
        $account = User::whereEmail($email)->first();

        if ($account) {
            return $account;
        } else {
            $account = User::create([
                'name' => $providerUser->getName(),
                'email' => $email,
                'password' => md5(rand(1, 10000)),
                'avatar' => $providerUser->getAvatar(),
            ]);

            return $account;
        }
    }
}
