<?php

namespace App\Application;

class Request
{
    private static array $params = [];

    public static function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    public static function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public static function getPostField(string $field): string
    {
        return htmlspecialchars($_POST[$field]) ?? '';
    }

    public static function getSession(): array
    {
        return $_SESSION ?? [];
    }

    public static function setParams(array $params): void
    {
        self::$params = $params;
    }

    public static function getParams(): array
    {
        return self::$params;
    }

    public static function getParam(string $key, $default = null)
    {
        return self::$params[$key] ?? $default;
    }

    public static function getUrlParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
