<?php

namespace WJM\Infra\Services;

interface EmailSenderInterface
{
    public function send(string $to, string $subject, string $body): bool;
}