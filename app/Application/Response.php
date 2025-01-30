<?php

namespace App\Application;

class Response
{
    private int $statusCode = 200;
    private mixed $content;

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function setContent(mixed $content): void
    {
        $this->content = $content;
    }

    public function json()
    {
        header('Content-Type: application/json');
        http_response_code($this->statusCode);
        echo json_encode($this->content);
    }

    public static function redirect(string $url): void
    {
        header("Location: $url");
        http_response_code(302);
        exit;
    }
}
