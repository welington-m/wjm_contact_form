<?php
function wjm_render_form_editor() {
    global $wpdb;
    $is_edit = isset($_GET['id']);
    $form = null;

    if ($is_edit) {
        $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wjm_forms WHERE id = %d", $_GET['id']));
    }

    echo '<div class="wrap"><h1>' . ($is_edit ? 'Editar' : 'Novo') . ' Formulário</h1>';
    echo '<form method="post">';
    wp_nonce_field('wjm_save_form', 'wjm_form_nonce');

    echo '<table class="form-table">';
    echo '<tr><th><label for="wjm_form_title">Título</label></th><td><input type="text" name="wjm_form_title" value="' . esc_attr($form->title ?? '') . '" required></td></tr>';
    echo '<tr><th><label for="wjm_form_slug">Slug</label></th><td><input type="text" name="wjm_form_slug" value="' . esc_attr($form->slug ?? '') . '"></td></tr>';
    echo '<tr><th><label for="wjm_form_config">Editor de Formulário (JSON)</label></th><td><textarea name="wjm_form_config" rows="10" cols="70" required>' . esc_textarea($form->config ?? '{"fields":[]}') . '</textarea></td></tr>';
    echo '</table>';

    if ($is_edit) {
        echo '<input type="hidden" name="wjm_form_id" value="' . esc_attr($form->id) . '">';
    }

    submit_button('Salvar Formulário');

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

    echo '</form></div>';
}
