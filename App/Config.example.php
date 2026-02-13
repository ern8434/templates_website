<?php

namespace App;

class Config {
    // Replace this with your actual Envato Personal Token
    // You can generate one at https://build.envato.com/create-token/
    // Permissions needed: 'View and search Envato sites'
    public const ENVATO_API_TOKEN = 'YOUR_ENVATO_API_TOKEN_HERE';

    public static function getApiToken(): string {
        return self::ENVATO_API_TOKEN;
    }
}
