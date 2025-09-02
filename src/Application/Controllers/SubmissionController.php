<?php

namespace WJM\Application\Controllers;

use WJM\Application\UseCase\Form\SubmitFormUseCase;
use WJM\Application\UseCase\Form\DTO\SubmissionDTO;
use WJM\Infra\Repositories\FormRepository;
use WJM\Infra\WordPress\View;

class SubmissionController
{
    public function __construct(
        private SubmitFormUseCase $submitUseCase,
        private View $view
    ) {}

    public function handle(int $formId, array $data): array
    {
        $dto = new SubmissionDTO($formId, $data);
        return $this->submitUseCase->execute($dto);
    }

    public function renderShortcode($atts): string
    {
        $formId = isset($atts['id']) ? (int)$atts['id'] : 0;

        if ($formId <= 0) {
            return '<p>Formulário inválido.</p>';
        }

        $repository = new FormRepository($GLOBALS['wpdb']);
        $form = $repository->findById($formId);

        if (!$form) {
            return '<p>Formulário não encontrado.</p>';
        }


        $html = $this->view->render('public/form', ['form' => $form]);

        return is_string($html) ? $html : '';
    }

public function handlePost(): void
{
    if (!isset($_POST['wjm_nonce']) || !wp_verify_nonce($_POST['wjm_nonce'], 'wjm_form_submit')) {
        wp_die('Falha na verificação de segurança');
    }

    $formId = isset($_POST['form_id']) ? (int) $_POST['form_id'] : 0;
    if ($formId <= 0) {
        wp_die('ID do formulário inválido.');
    }

    $formData = $_POST;

    try {
        $result = $this->handle($formId, $formData);

        if ($result['success']) {
            wp_redirect(add_query_arg([
                'form_status' => 'success',
                'message' => urlencode('Formulário enviado com sucesso!')
            ], wp_get_referer()));
        } else {
            wp_redirect(add_query_arg([
                'form_status' => 'error',
                'message' => urlencode($result['error'] ?? 'Erro ao enviar o formulário.')
            ], wp_get_referer()));
        }
        exit;
    } catch (\Throwable $e) {
        wp_redirect(add_query_arg([
            'form_status' => 'error',
            'message' => urlencode('Erro inesperado: ' . $e->getMessage())
        ], wp_get_referer()));
        exit;
    }
}

}