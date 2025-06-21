<?php
namespace UseCases;

use Entities\FormData;
use ValueObjects\Email;
use Services\EmailSender;
use Repositories\FormStorage;

class HandleContactForm {
    public function execute(array $data): void {
        $formData = new FormData(
            $data['name'] ?? '',
            new Email($data['email'] ?? ''),
            $data['message'] ?? '',
            $data['topic'] ?? ''
        );

        (new FormStorage())->save($formData);
        (new EmailSender())->send($formData);
    }
}