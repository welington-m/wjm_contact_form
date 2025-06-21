<?php
namespace Entities;

use ValueObjects\Email;

class FormData {
    public string $name;
    public Email $email;
    public string $message;
    public string $topic;

    public function __construct(string $name, Email $email, string $message, string $topic) {
        $this->name = trim($name);
        $this->email = $email;
        $this->message = trim($message);
        $this->topic = trim($topic);
    }
}