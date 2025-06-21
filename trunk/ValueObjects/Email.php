<?php
namespace ValueObjects;

class Email {
    private string $value;

    public function __construct(string $email) {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email inválido: $email");
        }
        $this->value = $email;
    }

    public function get(): string {
        return $this->value;
    }
}