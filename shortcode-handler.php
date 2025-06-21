<?php
function wjm_render_form_frontend($atts) {
    global $wpdb;
    $form_id = intval($atts['id'] ?? 0);
    if (!$form_id) return '';

    $table = $wpdb->prefix . 'wjm_forms';
    $form = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $form_id));
    if (!$form) return '<p>Formulário não encontrado.</p>';

    $fields = json_decode($form->config, true)['fields'] ?? [];

    // Validação de submissão
    $output = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_wjm_form_id']) && $_POST['_wjm_form_id'] == $form_id) {
        if (!isset($_POST['_wjm_nonce']) || !wp_verify_nonce($_POST['_wjm_nonce'], 'wjm_submit_' . $form_id)) {
            return '<p>Erro de segurança. Por favor, recarregue a página.</p>';
        }

        $errors = [];
        $submitted = [];
        foreach ($fields as $field) {
            $name = sanitize_text_field($field['name']);
            $value = isset($_POST[$name]) ? trim($_POST[$name]) : '';
            if (!empty($field['required']) && empty($value)) {
                $errors[] = $field['label'] . ' é obrigatório.';
            }
            $submitted[$name] = sanitize_text_field($value);
        }

        if (empty($errors)) {
            $output .= '<div class="wjm-success">Formulário enviado com sucesso!</div>';
        } else {
            $output .= '<div class="wjm-errors"><ul><li>' . implode('</li><li>', $errors) . '</li></ul></div>';
        }
    }

    // Renderização do formulário
    ob_start();
    echo $output;
    echo '<form method="post" class="wjm-public-form">';
    wp_nonce_field('wjm_submit_' . $form_id, '_wjm_nonce');
    echo '<input type="hidden" name="_wjm_form_id" value="' . esc_attr($form_id) . '">';

    foreach ($fields as $field) {
        $name = esc_attr($field['name']);
        $label = esc_html($field['label']);
        $placeholder = esc_attr($field['placeholder'] ?? '');
        $required = !empty($field['required']) ? 'required' : '';
        $type = $field['type'] ?? 'text';

        echo '<div class="wjm-field">';
        echo '<label for="' . $name . '">' . $label . '</label>';

        if ($type === 'select') {
            $options = $field['options'] ?? [];
            echo '<select name="' . $name . '" id="' . $name . '" ' . $required . '>';
            echo '<option value="">Selecione...</option>';
            foreach ($options as $opt) {
                $opt_val = esc_attr($opt);
                echo '<option value="' . $opt_val . '">' . esc_html($opt) . '</option>';
            }
            echo '</select>';
        } else {
            echo '<input type="' . esc_attr($type) . '" name="' . $name . '" id="' . $name . '" placeholder="' . $placeholder . '" ' . $required . '>';
        }

        echo '</div>';
    }

    echo '<button type="submit" class="wjm-submit-button">Enviar</button>';
    echo '</form>';
    return ob_get_clean();
}

add_shortcode('wjm_form', 'wjm_render_form_frontend');
