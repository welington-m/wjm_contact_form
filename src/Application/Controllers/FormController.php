<?php

namespace WJM\Application\Controllers;

use WJM\Application\UseCase\Form\CreateFormUseCase;
use WJM\Application\UseCase\Form\UpdateFormUseCase;
use WJM\Application\UseCase\Form\GetFormUseCase;
use WJM\Application\UseCase\Form\ListFormsUseCase;
use WJM\Application\UseCase\Form\DTO\CreateFormDTO;
use WJM\Application\UseCase\Form\DTO\UpdateFormDTO;
use WJM\Infra\WordPress\Helpers\UrlHelper;
use WJM\Infra\WordPress\View;

class FormController
{
    public function __construct(
        private CreateFormUseCase $createFormUseCase,
        private UpdateFormUseCase $updateFormUseCase,
        private GetFormUseCase $getFormUseCase,
        private ListFormsUseCase $listFormsUseCase,
        private View $view,
        private UrlHelper $urlHelper
    ) {}

    public function index(): void
    {
        $this->view->render('admin/forms/index', [
            'forms' => $this->listFormsUseCase->execute(),
            'new_form_url' => $this->urlHelper->getNewFormUrl()
        ]);
    }

    public function edit(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $form = $this->getFormUseCase->execute((int) $id);
        } else {
            $form = null;
        }

        $this->view->render('admin/forms/edit', ['form' => $form]);
    }

    public function style(): void
    {
        $id = $_GET['id'] ?? null;

        if ($id) {
            $form = $this->getFormUseCase->execute((int) $id);
        } else {
            $form = null;
        }

        $this->view->render('admin/forms/style', ['form' => $form]);
    }

    public function save(array $request): bool
    {
        $fields = $request['fields']['fields'] ?? $request['fields'] ?? [];

        if (empty($request['id'])) {
            $dto = new CreateFormDTO(
                title: $request['name'] ?? 'Formulário sem nome',
                fields: $fields,
                recipientEmail: $request['recipient'] ?? null,
                submitButtonText: $request['submit_button_text'] ?? 'Enviar',
                errorMessage: $request['error_message'] ?? '',
                successMessage: $request['success_message'] ?? '',
                layout: isset($request['layout']) ? (int) $request['layout'] : 1
            );

            $this->createFormUseCase->execute($dto);
        } else {
            $dto = new UpdateFormDTO(
                id: (int) $request['id'],
                title: $request['name'] ?? 'Formulário sem nome',
                fields: $fields,
                recipientEmail: $request['recipient'] ?? null,
                submitButtonText: $request['submit_button_text'] ?? 'Enviar',
                errorMessage: $request['error_message'] ?? '',
                successMessage: $request['success_message'] ?? '',
                layout: isset($request['layout']) ? (int) $request['layout'] : 1
            );

            $this->updateFormUseCase->execute($dto);
        }

        return true;
    }

    public function handleSave(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para isso.', 'wjm'));
        }

        $postData = $_POST;
        $postData['fields'] = json_decode(stripslashes($postData['fields'] ?? '[]'), true);

        $this->save($postData);

        $redirectUrl = admin_url('admin.php?page=wjm_forms');
        wp_redirect($redirectUrl);
        exit;
    }

    public function saveStyle(array $request): bool
    {
        if (empty($request['id'])) {
            return false;
        }

        $existingForm = $this->getFormUseCase->execute((int) $request['id']);

        if (!$existingForm) {
            return false;
        }

        $dto = new UpdateFormDTO(
            id: $existingForm->id,
            title: $existingForm->title,
            fields: $request['fields'] ?? $existingForm->fields,
            recipientEmail: $existingForm->recipientEmail,
            submitButtonText: $existingForm->submitButtonText,
            errorMessage: $existingForm->errorMessage,
            successMessage: $existingForm->successMessage,
            layout: isset($request['layout']) ? (int) $request['layout'] : $existingForm->layout,
        );

        $this->updateFormUseCase->execute($dto);

        return true;
    }

    public function handleSaveStyle(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para isso.', 'wjm'));
        }

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id <= 0 || !wp_verify_nonce($_POST['_wpnonce'] ?? '', 'wjm_save_form_style_' . $id)) {
            wp_die(__('Ação não autorizada.', 'wjm'), 'Erro', ['response' => 403]);
        }

        $postData = $_POST;
        $postData['fields'] = json_decode(stripslashes($postData['fields'] ?? '[]'), true);

        $this->saveStyle($postData);

        $redirectUrl = admin_url('admin.php?page=wjm_form_editor&id=' . $id);
        wp_redirect($redirectUrl);
        exit;
    }

    public function delete(): bool
    {
        $formId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($formId <= 0 || !wp_verify_nonce($_GET['_wpnonce'] ?? '', 'wjm_delete_form_' . $formId)) {
            wp_die('Ação não autorizada.', 'Erro', ['response' => 403]);
        }
        $formRepository = new \WJM\Infra\Repositories\FormRepository($GLOBALS['wpdb']);

        $formRepository->delete($formId);

        wp_redirect(admin_url('admin.php?page=wjm_forms&deleted=1'));
        exit;
    }
}
