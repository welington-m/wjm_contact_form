<?php

namespace WJM;

use wpdb;
use WJM\Infra\WordPress\View;
use WJM\Infra\WordPress\AssetsLoader;
use WJM\Infra\WordPress\AdminMenu;
use WJM\Infra\WordPress\Helpers\UrlHelper;
use WJM\Infra\Repositories\FormRepository;
use WJM\Infra\Repositories\FormMessageRepository;
use WJM\Infra\Services\EmailSender;
use WJM\Domain\Factories\FormFactory;
use WJM\Domain\Factories\FormMessageFactory;
use WJM\Application\UseCase\Form\CreateFormUseCase;
use WJM\Application\UseCase\Form\UpdateFormUseCase;
use WJM\Application\UseCase\Form\SubmitFormUseCase;
use WJM\Application\UseCase\Form\GetFormUseCase;
use WJM\Application\UseCase\Form\ListFormsUseCase;
use WJM\Application\Controllers\DashboardController;
use WJM\Application\Controllers\FormController;
use WJM\Application\Controllers\SubmissionController;
use WJM\Application\Controllers\ExportController;
use WJM\Application\Controllers\FormMessageController;
use WJM\Infra\Hooks\HookRegistrar;

class PluginKernel
{
    public function __construct(private wpdb $wpdb) {}

    public function register(): void
    {
        // ========== CORE COMPONENTS ==========
        $view = new View();
        $urlHelper = new UrlHelper(WJM_PLUGIN_FILE);

        // ========== REPOSITORIES ==========
        $formRepository = new FormRepository($this->wpdb);
        $formMessageRepository = new FormMessageRepository($this->wpdb);
        
        // ========== SERVICES ==========
        $emailSender = new EmailSender();

        // ========== FACTORIES ==========
        $formFactory = new FormFactory();
        $formMessageFactory = new FormMessageFactory();

        // ========== USE CASES ==========
        $createFormUseCase = new CreateFormUseCase($formRepository);
        $updateFormUseCase = new UpdateFormUseCase($formRepository, $formFactory);
        $submitFormUseCase = new SubmitFormUseCase($formRepository, $formMessageRepository, $emailSender, $formMessageFactory);
        $getFormUseCase = new GetFormUseCase($formRepository);
        $listFormsUseCase = new ListFormsUseCase($formRepository);

        // ========== CONTROLLERS ==========
        $dashboardController = new DashboardController(
            $listFormsUseCase,
            $view,
            $urlHelper
        );

        $formEditorController = new FormController(
            $createFormUseCase,
            $updateFormUseCase,
            $getFormUseCase,
            $listFormsUseCase,
            $view,
            $urlHelper
        );

        $submissionController = new SubmissionController(
            $submitFormUseCase,
            $view
        );

        $exportController = new ExportController(
            $formMessageRepository
        );

        $formMessageController = new FormMessageController(
            $formMessageRepository,
            $view
        );

        // ========== HOOK REGISTRATION ==========
        $adminMenu = new AdminMenu(
            $dashboardController,
            $formEditorController,
            $formMessageController
        );

        $assetsLoader = new AssetsLoader();

        (new HookRegistrar(
            $adminMenu, 
            $assetsLoader, 
            $formEditorController, 
            $submissionController, 
            $formMessageController,
            $exportController
        ))->register();

        // ========== SHORTCODES ==========
        add_shortcode('contact-form', [$submissionController, 'renderShortcode']);
    }
}