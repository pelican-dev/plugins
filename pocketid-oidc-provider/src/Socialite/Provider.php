<?php

namespace SocialiteProviders\PocketID;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getConfig('base_url') . '/oauth/authorize', $state);
    }

    protected function getTokenUrl()
    {
        return $this->getConfig('base_url') . '/oauth/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getConfig('base_url') . '/oauth/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id' => $user['sub'],
            'name' => $user['name'] ?? null,
            'email' => $user['email'] ?? null,
        ]);
    }
}