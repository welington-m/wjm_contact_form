<?php
/**
 * Plugin Name: WJM Contact Form
 * Description: Plugin de formulário de contato com arquitetura DDD, Clean Architecture e Shortcode.
 * Version: 1.1.3
 * Author: Welington Jose Miyazato
 */


defined('ABSPATH') || exit;

// 1. Autoload das classes (Composer ou PSR-4 manual)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// 2. Define a constante principal do plugin
if (!defined('WJM_PLUGIN_FILE')) {
    define('WJM_PLUGIN_FILE', __FILE__);
}

register_activation_hook(__FILE__, function () {
    \WJM\Infra\Database\FormTableMigrator::migrate($GLOBALS['wpdb']);
});

(new \WJM\PluginKernel($GLOBALS['wpdb']))->register();

