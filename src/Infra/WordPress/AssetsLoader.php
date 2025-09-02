<?php

namespace WJM\Infra\WordPress;

class AssetsLoader
{
    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueuePublicAssets']);
    }

    public function enqueueAdminAssets(string $hook): void
    {
        // CSS administrativo global
        wp_enqueue_style(
            'wjm-admin-style',
            plugins_url('assets/css/admin.css', WJM_PLUGIN_FILE),
            [],
            filemtime(plugin_dir_path(WJM_PLUGIN_FILE) . 'assets/css/admin.css')
        );

        // JS administrativo
        wp_enqueue_script(
            'wjm-admin-js',
            plugins_url('assets/js/admin.js', WJM_PLUGIN_FILE),
            ['jquery'],
            filemtime(plugin_dir_path(WJM_PLUGIN_FILE) . 'assets/js/admin.js'),
            true
        );
    }

    public function enqueuePublicAssets(): void
    {
        // CSS público
        wp_enqueue_style(
            'wjm-public-style',
            plugins_url('assets/css/public.css', WJM_PLUGIN_FILE),
            [],
            filemtime(plugin_dir_path(WJM_PLUGIN_FILE) . 'assets/css/public.css')
        );

        // JS público
        wp_enqueue_script(
            'wjm-public-js',
            plugins_url('assets/js/public.js', WJM_PLUGIN_FILE),
            ['jquery'],
            filemtime(plugin_dir_path(WJM_PLUGIN_FILE) . 'assets/js/public.js'),
            true
        );
    }
}
