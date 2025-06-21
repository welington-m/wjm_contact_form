<?php
namespace Services;

use Entities\FormData;

class EmailSender {
    public function send(FormData $data): void {
        $to = get_option('admin_email');
        $subject = 'Nova mensagem de contato';
        $message = "Nome: {$data->name}\nEmail: {$data->email->get()}\nMensagem: {$data->message}";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        wp_mail($to, $subject, $message, $headers);
    }
}