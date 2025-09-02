<?php

namespace WJM\Infra\Services;

use WJM\Infra\Services\EmailSenderInterface;

class EmailSender implements EmailSenderInterface
{
    public function send(string $to, string $subject, string $body): bool
    {
        // Validação robusta do destinatário
        if (!is_email($to)) {
            error_log('WJM EmailSender: Email do destinatário inválido, usando email admin');
            $to = get_option('admin_email');
            
            if (!is_email($to)) {
                error_log('WJM EmailSender CRITICAL: Email admin também é inválido!');
                return false;
            }
        }

        // Email do remetente seguro
        $from_email = get_option('admin_email');
        if (!is_email($from_email)) {
            $from_email = 'noreply@' . parse_url(home_url(), PHP_URL_HOST);
            error_log('WJM EmailSender: Usando email padrão do domínio como remetente');
        }

        $site_name = get_bloginfo('name');
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $site_name . ' <' . $from_email . '>',
            'Reply-To: ' . $from_email
        ];

        // Registrar tentativa de envio
        error_log(sprintf(
            'WJM EmailSender: Tentando enviar email para %s. Assunto: %s',
            $to,
            $subject
        ));

        // Configurar hook para capturar erros
        add_action('wp_mail_failed', function ($error) {
            error_log('WJM EmailSender ERROR: ' . $error->get_error_message());
        });

        $result = wp_mail($to, $subject, $body, $headers);

        if (!$result) {
            error_log('WJM EmailSender: Falha no envio do email');
            $last_error = error_get_last();
            if ($last_error) {
                error_log('WJM EmailSender: Último erro - ' . print_r($last_error, true));
            }
        } else {
            error_log('WJM EmailSender: Email enviado com sucesso');
        }

        return $result;
    }
}