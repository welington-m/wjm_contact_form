<?php
$options_raw = get_option('wjm_contact_select_options', "Alistamento\nContato");
$options_array = array_filter(array_map('trim', explode("\n", $options_raw)));
$select_options = array_combine($options_array, $options_array); // label = value

$options = [
    'name_label' => get_option('wjm_contact_label_name', 'Seu nome'),
    'email_label' => get_option('wjm_contact_label_email', 'Seu email'),
    'message_label' => get_option('wjm_contact_label_message', 'Mensagem'),
    'topic_label' => get_option('wjm_contact_label_topic', 'Assunto'),
    'select_options' => $select_options,
    'required_message' => get_option('wjm_contact_required_message', 'Campo obrigatório'),
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wjm_contact_nonce_field']) && wp_verify_nonce($_POST['wjm_contact_nonce_field'], 'wjm_contact_nonce')) {
    $required = ['name', 'email', 'message', 'topic'];
    foreach ($required as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[$field] = $options['required_message'];
        }
    }

    if (!in_array($_POST['topic'], array_keys($select_options))) {
        $errors['topic'] = 'Valor inválido selecionado';
    }

    if (empty($errors)) {
        require_once __DIR__ . '/UseCases/HandleContactForm.php';
        (new \UseCases\HandleContactForm())->execute($_POST);
        echo '<p class="notice-success">Mensagem enviada com sucesso!</p>';
    }
}
?>

<form method="POST">
    <?php wp_nonce_field('wjm_contact_nonce', 'wjm_contact_nonce_field'); ?>

    <p>
        <label><?= esc_html($options['name_label']) ?> *</label><br>
        <input name="name" value="<?= esc_attr($_POST['name'] ?? '') ?>">
        <?php if (isset($errors['name'])) echo '<span class="error">' . esc_html($errors['name']) . '</span>'; ?>
    </p>

    <p>
        <label><?= esc_html($options['email_label']) ?> *</label><br>
        <input name="email" type="email" value="<?= esc_attr($_POST['email'] ?? '') ?>">
        <?php if (isset($errors['email'])) echo '<span class="error">' . esc_html($errors['email']) . '</span>'; ?>
    </p>

    <p>
        <label><?= esc_html($options['message_label']) ?> *</label><br>
        <textarea name="message"><?= esc_textarea($_POST['message'] ?? '') ?></textarea>
        <?php if (isset($errors['message'])) echo '<span class="error">' . esc_html($errors['message']) . '</span>'; ?>
    </p>

    <p>
        <label><?= esc_html($options['topic_label']) ?> *</label><br>
        <select name="topic">
            <option value="">-- Selecione --</option>
            <?php foreach ($options['select_options'] as $value => $label) : ?>
                <option value="<?= esc_attr($value) ?>" <?= (($_POST['topic'] ?? '') === $value) ? 'selected' : '' ?>><?= esc_html($label) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['topic'])) echo '<span class="error">' . esc_html($errors['topic']) . '</span>'; ?>
    </p>

    <button type="submit">Enviar</button>
</form>