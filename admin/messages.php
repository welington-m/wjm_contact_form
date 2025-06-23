<?php
// admin/messages.php

use Repositories\MessageRepository;

function wjm_render_messages_admin() {
    global $wpdb;
    $repo = new MessageRepository($wpdb);

    $form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
    $start = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    $end = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

    $messages = $repo->getMessages($form_id, $start, $end);

    // Buscar formulários existentes
    $forms = $wpdb->get_results("SELECT id, title FROM {$wpdb->prefix}wjm_forms");

    echo '<div class="wrap">';
    echo '<h1>📨 Mensagens Recebidas</h1>';
    echo '<form method="get" action="">';
    echo '<input type="hidden" name="page" value="wjm-manage-messages">';

    echo '<label for="form_id">Formulário:</label> ';
    echo '<select name="form_id">';
    echo '<option value="0">Todos</option>';
    foreach ($forms as $f) {
        $selected = $form_id == $f->id ? 'selected' : '';
        echo "<option value='{$f->id}' {$selected}>{$f->title}</option>";
    }
    echo '</select> ';

    echo '<label for="start_date">De:</label> ';
    echo '<input type="date" name="start_date" value="' . esc_attr($start) . '"> ';
    echo '<label for="end_date">Até:</label> ';
    echo '<input type="date" name="end_date" value="' . esc_attr($end) . '"> ';
    echo '<input type="submit" class="button" value="Filtrar">';
    echo '</form><br>';

    if ($messages) {
        echo '<table class="widefat striped">';
        echo '<thead><tr><th>Formulário</th><th>Data</th><th>Conteúdo</th></tr></thead><tbody>';
        foreach ($messages as $msg) {
            echo '<tr>';
            echo '<td>' . esc_html($msg->form_id) . '</td>';
            echo '<td>' . esc_html($msg->created_at) . '</td>';
            echo '<td><pre>' . esc_html($msg->data) . '</pre></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>Nenhuma mensagem encontrada para os critérios selecionados.</p>';
    }

    echo '</div>';
}

add_action('admin_menu', function () {
    add_submenu_page(
        'wjm-forms',
        'Gerenciar Mensagens',
        'Gerenciar Mensagens',
        'manage_options',
        'wjm-manage-messages',
        'wjm_render_messages_admin'
    );
});
