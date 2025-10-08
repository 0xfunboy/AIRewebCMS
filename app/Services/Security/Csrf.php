<?php
declare(strict_types=1);

namespace App\Services\Security;

use App\Support\Session;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string
    {
        Session::ensureStarted();

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = $token;

        return $token;
    }

    public static function verify(?string $token): bool
    {
        Session::ensureStarted();

        $stored = $_SESSION[self::SESSION_KEY] ?? null;
        unset($_SESSION[self::SESSION_KEY]);

        if (!$stored || !$token) {
            return false;
        }

        return hash_equals($stored, $token);
    }
}
