<?php
function wjm_render_forms_list() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'wjm_forms';

    // Processar exclusão, se aplicável
    if (isset($_POST['wjm_delete_form']) && isset($_POST['form_id']) && check_admin_referer('wjm_delete_form_' . $_POST['form_id'])) {
        $wpdb->delete($table_name, ['id' => (int) $_POST['form_id']]);
        echo '<div class="notice notice-success"><p>Formulário excluído com sucesso.</p></div>';
    }

    $forms = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    echo '<div class="wrap">';
    echo '<h1 class="wp-heading-inline">Formulários WJM</h1>';
    echo ' <a href="admin.php?page=wjm-form-editor" class="page-title-action">Adicionar Novo</a>';
    echo ' <a href="admin.php?page=wjm-messages" class="page-title-action">Gerenciar Mensagens</a>';
    echo '<hr class="wp-header-end">';

    if ($forms) {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr><th>ID</th><th>Título</th><th>Shortcode</th><th>Criado em</th><th>Ações</th></tr></thead><tbody>';
        foreach ($forms as $form) {
            $shortcode = '[wjm_form id=' . esc_attr($form->id) . ']';
            echo '<tr>';
            echo '<td>' . esc_html($form->id) . '</td>';
            echo '<td>' . esc_html($form->title) . '</td>';
            echo '<td><code>' . esc_html($shortcode) . '</code></td>';
            echo '<td>' . esc_html($form->created_at) . '</td>';
            echo '<td>';
            echo '<a class="button" href="admin.php?page=wjm-form-editor&id=' . esc_attr($form->id) . '">Editar</a> ';
            echo '<form method="post" style="display:inline-block" onsubmit="return confirm(\'Tem certeza que deseja excluir este formulário?\');">';
            echo '<input type="hidden" name="form_id" value="' . esc_attr($form->id) . '">';
            wp_nonce_field('wjm_delete_form_' . $form->id);
            echo '<input type="submit" name="wjm_delete_form" class="button button-danger" value="Excluir">';
            echo '</form> ';
            echo '<button class="button" onclick="navigator.clipboard.writeText(\'' . esc_js($shortcode) . '\')">Copiar</button>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>Nenhum formulário encontrado.</p>';
    }
    echo '</div>';
}