<?php

namespace WJM\Infra\WordPress;

use WJM\Application\Controllers\DashboardController;
use WJM\Application\Controllers\FormController;
use WJM\Application\Controllers\FormMessageController;

class AdminMenu
{
    public function __construct(
        private DashboardController $dashboardController,
        private FormController $formController,
        private FormMessageController $formMessageController
    ) {}
    public function register(): void
    {
        add_action('admin_menu', function () {
            add_menu_page(
                'WJM Contact Form',
                'WJM Contact Form',
                'manage_options',
                'wjm_contact_dashboard',
                [$this->dashboardController, 'show'],
                'dashicons-email-alt',
                25
            );

            add_submenu_page(
                'wjm_contact_dashboard',
                'Listar Formulários',
                'Formulários',
                'manage_options', 
                'wjm_forms', 
                [$this->formController, 'index'] 
            );

            add_submenu_page(
                null,
                'Form Editor',
                'Form Editor',
                'manage_options',
                'wjm_form_editor',
                [$this->formController, 'edit']
            );

            add_submenu_page(
                null,
                'Estilizar Formulário',
                'Estilizar Formulário',
                'manage_options',
                'wjm_form_style',
                [$this->formController, 'style']
            );

            add_submenu_page(
                'wjm_contact_dashboard',
                'Listar Mensagens',
                'Listar Mensagens',
                'manage_options',
                'wjm_form_messages',
                [$this->formMessageController, 'list']
            );

            add_submenu_page(
                'wjm_contact_dashboard',
                'Detalhes da Mensagem',
                'Detalhes da Mensagem',
                'manage_options',
                'wjm_view_message',
                [$this->formMessageController, 'show']
            );
        });

        add_action('admin_menu', function () {
            remove_submenu_page('wjm_contact_dashboard', 'wjm_view_message');
        }, 999);
    }
}
