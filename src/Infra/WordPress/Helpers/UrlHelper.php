<?php

namespace WJM\Infra\WordPress\Helpers;

class UrlHelper
{
    private string $pluginFile;

    public function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
    }

    /**
     * Retorna a URL do banner padrão.
     */
    public function getBannerUrl(string $relativePath = 'assets/img/banner-470x152.png'): string
    {
        return plugins_url($relativePath, $this->pluginFile);
    }

    
    /**
     * Retorna a URL da tela de novo formulário.
     */
    public function getNewFormUrl(): string
    {
        return admin_url('admin.php?page=wjm_form_editor');
    }


    /**
     * Retorna a URL da tela de mensagens.
     */
    public function getMessagesUrl(): string
    {
        return admin_url('admin.php?page=wjm_form_messages');
    }
}
