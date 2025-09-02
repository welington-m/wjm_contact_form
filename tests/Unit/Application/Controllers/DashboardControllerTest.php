<?php

namespace WJM\Infra\WordPress\Helpers;

// Mock da função admin_url do WordPress
if (!function_exists(__NAMESPACE__ . '\admin_url')) {
    function admin_url(string $path = ''): string
    {
        return 'http://localhost:8001/wp-admin/' . ltrim($path, '/');
    }
}

if (!function_exists(__NAMESPACE__ . '\plugins_url')) {
    function plugins_url(string $path = '', string $plugin_file = ''): string
    {
        return 'http://localhost:8001/wp-content/plugins/wjm-contact-form-inicial/' . ltrim($path, '/');
    }
}

namespace Tests\Unit\Application\Controllers;

use PHPUnit\Framework\TestCase;
use WJM\Application\Controllers\DashboardController;
use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Infra\WordPress\View;
use WJM\Infra\WordPress\Helpers\UrlHelper;


class DashboardControllerTest extends TestCase
{
    public function testShowRendersDashboardViewWithCorrectData(): void
    {
        $formRepositoryMock = $this->createMock(FormRepositoryInterface::class);
        $formRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $viewMock = $this->createMock(View::class);
        $viewMock
            ->expects($this->once())
            ->method('render')
            ->with(
                'admin/dashboard',
                $this->callback(function ($data) {
                    return isset($data['forms'], $data['new_form_url'], $data['messages_url'], $data['banner_url']);
                })
            );
        $urlHelper = new UrlHelper('/fake/plugin/file.php');

        $controller = new DashboardController($formRepositoryMock, $viewMock, $urlHelper);
        $controller->show();
    }
}
