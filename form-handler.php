<?php
function wjm_process_form_submission($form_id, $fields) {
    $errors = [];
    $values = [];

    foreach ($fields as $field) {
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $required = $field['required'] ?? false;

        $value = sanitize_text_field($_POST[$name] ?? '');

        if ($required && empty($value)) {
            $errors[] = "$label é obrigatório.";
        }

        $values[$name] = $value;
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    // Salvar mensagem no banco de dados
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'wjm_form_messages',
        [
            'form_id' => $form_id,
            'data' => maybe_serialize($values),
            'submitted_at' => current_time('mysql')
        ]
    );

    // Você pode colocar lógica de envio de e-mail aqui, se quiser

    return ['success' => true];
}
