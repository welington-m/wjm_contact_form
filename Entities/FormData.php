<?php
namespace Entities;

use ValueObjects\Email;

class FormData {
    public string $name;
    public Email $email;
    public string $message;

    public function __construct(string $name, Email $email, string $message) {
        $this->name = trim($name);
        $this->email = $email;
        $this->message = trim($message);
    }
}