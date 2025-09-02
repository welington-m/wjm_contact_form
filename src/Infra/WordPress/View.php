<?php

namespace WJM\Infra\WordPress;

class View
{
    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath ?: plugin_dir_path(WJM_PLUGIN_FILE) . 'views/';
    }

    public function render(string $template, array $data = []): void
    {
        $templatePath = rtrim($this->basePath, '/') . '/' . str_replace(['.', '\\'], ['/', '/'], $template) . '.php';

        if (!file_exists($templatePath)) {
            echo "<div class='notice notice-error'><p>Template não encontrado: {$templatePath}</p></div>";
            return;
        }

        extract($data);
        include $templatePath;
    }
}
