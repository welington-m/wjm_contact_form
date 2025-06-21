<?php
// Responsável pelo tratamento das submissões de formulário WJM

function wjm_handle_form_submission($form_id, $config) {
    $errors = [];
    $submitted = [];

    foreach ($config['fields'] as $field) {
        $name = sanitize_text_field($field['name']);
        $value = isset($_POST[$name]) ? trim($_POST[$name]) : '';

        if (!empty($field['required']) && empty($value)) {
            $errors[$name] = $field['label'] . ' é obrigatório.';
        }

        $submitted[$name] = sanitize_text_field($value);
    }

    if (!empty($errors)) {
        return ['errors' => $errors, 'values' => $submitted];
    }

    // Exemplo: aqui você pode enviar e-mail ou armazenar os dados
    // mail(...), wp_insert_post(...), etc.

    return ['success' => true, 'values' => $submitted];
}
?>