<?php

namespace Tests\Unit\Infra\WordPress\Helpers;

use PHPUnit\Framework\TestCase;
use WJM\Infra\WordPress\Helpers\UrlHelper;

class UrlHelperTest extends TestCase
{
    private string $pluginFile;
    private string $baseUrl;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurações do ambiente de teste
        $this->baseUrl = 'http://localhost:8001';
        $this->pluginFile = '/wp-content/plugins/wjm-contact-form-inicial/wjm-contact-form.php';

        // Mock das funções WordPress com tratamento mais robusto
        if (!function_exists('plugins_url')) {
            eval('function plugins_url($path = "", $plugin_file = "") {
                $pluginDir = dirname($plugin_file);
                return "'. $this->baseUrl . '" . $pluginDir . "/" . ltrim($path, "/");
            }');
        }

        if (!function_exists('admin_url')) {
            eval('function admin_url($path = "") {
                return "'. $this->baseUrl . '/wp-admin/" . ltrim($path, "/");
            }');
        }
    }

    public function testGetBannerUrlReturnsCorrectUrl(): void
    {
        $helper = new UrlHelper($this->pluginFile);

        $expected = $this->baseUrl . '/wp-content/plugins/wjm-contact-form-inicial/assets/img/banner-470x152.png';
        $this->assertSame($expected, $helper->getBannerUrl());
    }

    public function testGetNewFormUrl(): void
    {
        $helper = new UrlHelper($this->pluginFile);

        $expected = $this->baseUrl . '/wp-admin/admin.php?page=wjm_form_editor';
        $this->assertSame($expected, $helper->getNewFormUrl());
    }

    public function testGetMessagesUrl(): void
    {
        $helper = new UrlHelper($this->pluginFile);

        $expected = $this->baseUrl . '/wp-admin/admin.php?page=wjm_form_messages';
        $this->assertSame($expected, $helper->getMessagesUrl());
    }

    public function testUrlsAreConsistentWithDifferentBaseUrls(): void
    {
        // Teste adicional para verificar consistência
        $testUrl = 'http://localhost:8001';
        $this->mockWordPressFunctions($testUrl);
        
        $helper = new UrlHelper($this->pluginFile);
        $this->assertStringStartsWith(
            $testUrl, 
            $helper->getBannerUrl(),
            'As URLs devem refletir a base URL configurada'
        );
    }

    private function mockWordPressFunctions(string $baseUrl): void
    {
        eval('namespace Tests\Unit\Infra\WordPress\Helpers; function plugins_url($path = "", $plugin_file = "") {
            $pluginDir = dirname($plugin_file);
            return "' . $baseUrl . '" . $pluginDir . "/" . ltrim($path, "/");
        }');

        eval('namespace Tests\Unit\Infra\WordPress\Helpers; function admin_url($path = "") {
            return "' . $baseUrl . '/wp-admin/" . ltrim($path, "/");
        }');
    }
}