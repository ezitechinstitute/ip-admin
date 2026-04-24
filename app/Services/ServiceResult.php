<?php

namespace App\Services;

class ServiceResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly mixed $data = null,
        public readonly ?int $code = null
    ) {}

    public static function success(string $message = 'Operation successful', mixed $data = null, ?int $code = null): self
    {
        return new self(true, $message, $data, $code);
    }

    public static function error(string $message = 'Operation failed', mixed $data = null, ?int $code = null): self
    {
        return new self(false, $message, $data, $code);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function isError(): bool
    {
        return !$this->success;
    }
}