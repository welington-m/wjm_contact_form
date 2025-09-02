<?php

namespace WJM\Application\Controllers;

use WJM\Application\UseCase\Form\ListFormsUseCase;
use WJM\Infra\WordPress\View;
use WJM\Infra\WordPress\Helpers\UrlHelper;

class DashboardController
{
    public function __construct(
        private ListFormsUseCase $listFormsUseCase,
        private View $view,
        private UrlHelper $urlHelper
    ) {}

    public function show(): void
    {
        $this->view->render('admin/dashboard', [
            'forms' => $this->listFormsUseCase->execute(),
            'new_form_url' => $this->urlHelper->getNewFormUrl(),
            'messages_url' => $this->urlHelper->getMessagesUrl(),
            'banner_url' => $this->urlHelper->getBannerUrl(),
            'forms_count' => count($this->listFormsUseCase->execute())
        ]);
    }
}