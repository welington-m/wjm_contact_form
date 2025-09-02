<?php

namespace WJM\Infra\Hooks;

use WJM\Application\Controllers\ExportController;
use WJM\Application\Controllers\FormController;
use WJM\Application\Controllers\FormMessageController;
use WJM\Application\Controllers\SubmissionController;
use WJM\Infra\WordPress\AdminMenu;
use WJM\Infra\WordPress\AssetsLoader;

class HookRegistrar
{
    private AdminMenu $adminMenu;
    private AssetsLoader $assetsLoader;
    private FormController $formController;
    private SubmissionController $submissionController;
    private FormMessageController $formMessageController;
    private ExportController $exportController;

    public function __construct(
        AdminMenu $adminMenu,
        AssetsLoader $assetsLoader,
        FormController $formController,
        SubmissionController $submissionController,
        FormMessageController $formMessageController,
        ExportController $exportController
    ) {
        $this->adminMenu = $adminMenu;
        $this->assetsLoader = $assetsLoader;
        $this->formController = $formController;
        $this->submissionController = $submissionController;
        $this->formMessageController = $formMessageController;
        $this->exportController = $exportController;
    }

    public function register(): void
    {
        $this->adminMenu->register();
        $this->assetsLoader->register();

        // Aqui você pode adicionar outros hooks globais
        // add_action('init', [$this, 'algumaFuncaoGlobal']);
        
        // Formulários
        add_action('admin_post_wjm_save_form', [$this->formController, 'handleSave']);
        add_action('admin_post_wjm_delete_form', [$this->formController, 'delete']);
        
        // Submissões
        add_shortcode('wjm_form', [$this->submissionController, 'renderShortcode']);
        add_action('admin_post_wjm_submit_form', [$this->submissionController, 'handlePost']);
        add_action('admin_post_nopriv_wjm_submit_form', [$this->submissionController, 'handlePost']);
        
        // Mensagens
        add_action('wp_ajax_wjm_get_message_details', [$this->formMessageController, 'getMessageDetails']);
        add_action('admin_post_wjm_export_messages_csv', [$this->exportController, 'exportMessagesToCSV']);

    }
}
