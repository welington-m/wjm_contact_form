<?php
/*
Plugin Name: WJM Contact Form
Description: Plugin de contato com arquitetura limpa
Version: 1.1
Author: Welington Miyazato
*/

require_once plugin_dir_path(__FILE__) . 'shortcode-handler.php';

register_activation_hook(__FILE__, 'wjm_create_forms_table');
function wjm_create_forms_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wjm_forms';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE,
        config JSON NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('wjm-contact-admin-style', plugin_dir_url(__FILE__) . 'assets/admin.css');
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('wjm-contact-public-style', plugin_dir_url(__FILE__) . 'assets/form.css');
});

add_action('admin_menu', 'wjm_add_forms_menu');
function wjm_add_forms_menu() {
    add_menu_page(
        'WJM Formulários',
        'Formulários WJM',
        'manage_options',
        'wjm-forms',
        'wjm_render_forms_list',
        'dashicons-feedback',
        26
    );

    add_submenu_page(
        'wjm-forms',
        'Novo Formulário',
        'Adicionar Novo',
        'manage_options',
        'wjm-form-editor',
        'wjm_render_form_editor'
    );
}

function wjm_render_forms_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wjm_forms';
    $forms = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    echo '<div class="wrap wjm-admin-wrapper"><h1>Formulários WJM</h1>';
    echo '<p><a href="admin.php?page=wjm-form-editor" class="button button-primary">Adicionar Novo</a></p>';

    if ($forms) {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Título</th><th>Shortcode</th><th>Data</th><th>Ações</th></tr></thead><tbody>';

        foreach ($forms as $form) {
            $shortcode = '[wjm_form id=' . esc_attr($form->id) . ']';
            echo '<tr>';
            echo '<td>' . esc_html($form->id) . '</td>';
            echo '<td>' . esc_html($form->title) . '</td>';
            echo '<td><code>' . $shortcode . '</code></td>';
            echo '<td>' . esc_html($form->created_at) . '</td>';
            echo '<td>';
            echo '<a href="admin.php?page=wjm-form-editor&id=' . esc_attr($form->id) . '" class="button">Editar</a> ';
            echo '<form method="post" style="display:inline;" onsubmit="return confirm(\'Tem certeza que deseja excluir este formulário?\')">';
            echo '<input type="hidden" name="form_id" value="' . esc_attr($form->id) . '">';
            wp_nonce_field('wjm_delete_form_' . $form->id);
            echo '<input type="submit" name="wjm_delete_form" class="button" value="Excluir">';
            echo '</form> ';
            echo '<button class="button" onclick="navigator.clipboard.writeText(\'' . esc_js($shortcode) . '\')">Copiar</button>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>Nenhum formulário criado ainda.</p>';
    }
    echo '</div>';
}

function wjm_render_form_editor() {
    global $wpdb;
    $is_edit = isset($_GET['id']);
    $form = null;

    if ($is_edit) {
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wjm_forms WHERE id = %d", $_GET['id']));
    }

    echo '<div class="wrap wjm-admin-editor"><h1>' . ($is_edit ? 'Editar' : 'Novo') . ' Formulário</h1>';
    echo '<form method="post" action="">';
    wp_nonce_field('wjm_save_form', 'wjm_form_nonce');

    echo '<table class="form-table">';
    echo '<tr><th><label for="wjm_form_title">Título</label></th><td><input type="text" name="wjm_form_title" value="' . esc_attr($form->title ?? '') . '" required></td></tr>';
    echo '<tr><th><label for="wjm_form_slug">Slug</label></th><td><input type="text" name="wjm_form_slug" value="' . esc_attr($form->slug ?? '') . '"></td></tr>';
    echo '<tr><th><label for="wjm_form_config">JSON Configuração</label></th><td><textarea name="wjm_form_config" rows="10" cols="70" required>' . esc_textarea($form->config ?? '{"fields":[]}') . '</textarea></td></tr>';
    echo '</table>';

    if ($is_edit) {
        echo '<input type="hidden" name="wjm_form_id" value="' . esc_attr($form->id) . '">';
    }

    submit_button('Salvar Formulário');
    echo '</form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wjm_form_nonce']) && wp_verify_nonce($_POST['wjm_form_nonce'], 'wjm_save_form')) {
        $data = [
            'title' => sanitize_text_field($_POST['wjm_form_title']),
            'slug' => sanitize_title($_POST['wjm_form_slug']),
            'config' => wp_unslash($_POST['wjm_form_config'])
        ];

        if (isset($_POST['wjm_form_id'])) {
            $wpdb->update($wpdb->prefix . 'wjm_forms', $data, ['id' => (int) $_POST['wjm_form_id']]);
            echo '<div class="notice notice-success"><p>Formulário atualizado com sucesso.</p></div>';
        } else {
            $wpdb->insert($wpdb->prefix . 'wjm_forms', $data);
            echo '<div class="notice notice-success"><p>Formulário criado com sucesso.</p></div>';
        }
    }

    echo '</div>';
    
}
