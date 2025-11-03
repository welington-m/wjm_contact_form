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


        $attrLayout = isset($atts['layout']) ? (int) $atts['layout'] : null;
        $layout = in_array($attrLayout, [1, 2, 3, 4], true)
            ? $attrLayout
            : ($form->layout ?? 1);

        $attrColumns = isset($atts['columns']) ? (int)$atts['columns'] : null;
        $columns = in_array($attrColumns, [1, 2, 3, 4], true)
            ? $attrColumns
            : ($form->layout ?? 1);

        ob_start();
        $html = $this->view->render('public/form', [
            'form' => $form,
            'layout' => $layout,
            'columns' => $columns,
        ]);
        $bufferedOutput = ob_get_clean();

        if (is_string($html) && $html !== '') {
            return $html;
        }

        return $bufferedOutput !== false ? $bufferedOutput : '';
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

        $redirectBase = wp_get_referer();
        if (!$redirectBase) {
            $redirectBase = home_url('/');
        }

        if ($result['success']) {
            $redirectUrl = add_query_arg([
                'form_status' => 'success',
                'message' => $result['message'] ?? 'Formulário enviado com sucesso!'
            ], $redirectBase);
        } else {
            $redirectUrl = add_query_arg([
                'form_status' => 'error',
                'message' => $result['error'] ?? 'Erro ao enviar o formulário.'
            ], $redirectBase);
        }

        wp_safe_redirect($redirectUrl);
        exit;
    } catch (\Throwable $e) {
        $redirectBase = wp_get_referer();
        if (!$redirectBase) {
            $redirectBase = home_url('/');
        }

        $redirectUrl = add_query_arg([
            'form_status' => 'error',
            'message' => 'Erro inesperado: ' . $e->getMessage(),
        ], $redirectBase);

        wp_safe_redirect($redirectUrl);
        exit;
    }
}

}
