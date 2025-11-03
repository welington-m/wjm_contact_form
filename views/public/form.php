<?php
/** @var \WJM\Domain\Entities\Form $form */
/** @var int $columns */
if (!$form) {
    echo '<p>Formulário não encontrado.</p>';
    return;
}

$form_status = $_GET['form_status'] ?? '';
$message = $_GET['message'] ?? '';
?>

<div class="wjm-contact-form">
    <?php if (!empty($form_status) && !empty($message)): ?>
        <div class="<?php echo $form_status === 'success' ? 'wjm-success' : 'wjm-errors'; ?>">
            <?php echo esc_html($message); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="wjm_submit_form">
        <input type="hidden" name="form_id" value="<?php echo esc_attr($form->id); ?>">
        <?php wp_nonce_field('wjm_form_submit', 'wjm_nonce'); ?>

        <div class="wjm-grid columns-<?php echo $columns; ?>">
        <?php foreach ($form->fields as $field): ?>
            <?php
                $fieldClasses = ['wjm-form-group', 'col-' . max(1, min(4, (int)$field->column))];
                if ($field->fullWidth) {
                    $fieldClasses[] = 'full-width';
                }
            ?>
            <div class="<?php echo implode(' ', $fieldClasses); ?>">
                <label for="<?php echo esc_attr($field->name); ?>">
                    <?php echo esc_html($field->label); ?>
                    <?php if ($field->required): ?> *<?php endif; ?>
                </label>

                <?php if ($field->type === 'textarea'): ?>
                    <?php $placeholderAttr = $field->placeholder ? ' placeholder="' . esc_attr($field->placeholder) . '"' : ''; ?>
                    <textarea
                        id="<?php echo esc_attr($field->name); ?>"
                        name="<?php echo esc_attr($field->name); ?>"
                        <?php echo $field->required ? 'required' : ''; ?>
                        <?php echo $placeholderAttr; ?>
                    ></textarea>
                <?php elseif ($field->type === 'select'): ?>
                    <select
                        id="<?php echo esc_attr($field->name); ?>"
                        name="<?php echo esc_attr($field->name); ?>"
                        <?php echo $field->required ? 'required' : ''; ?>
                    >
                        <?php foreach ($field->options as $option): ?>
                            <option value="<?php echo esc_attr($option); ?>">
                                <?php echo esc_html($option); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($field->type === 'radio'): ?>
                    <!-- Grupo de radios com suporte a várias opções -->
                    <div class="wjm-options">
                        <?php foreach ($field->options as $option): ?>
                            <label class="wjm-option">
                                <input
                                    type="radio"
                                    id="<?php echo esc_attr($field->name . '_' . sanitize_title($option)); ?>"
                                    name="<?php echo esc_attr($field->name); ?>"
                                    value="<?php echo esc_attr($option); ?>"
                                    <?php echo $field->required ? 'required' : ''; ?>
                                >
                                <?php echo esc_html($option); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($field->type === 'checkbox'): ?>
                    <!-- Grupo de checkboxes com suporte a seleção múltipla -->
                    <div class="wjm-options">
                        <?php foreach ($field->options as $option): ?>
                            <label class="wjm-option">
                                <input
                                    type="checkbox"
                                    id="<?php echo esc_attr($field->name . '_' . sanitize_title($option)); ?>"
                                    name="<?php echo esc_attr($field->name); ?>[]"
                                    value="<?php echo esc_attr($option); ?>"
                                    <?php echo $field->required ? 'required' : ''; ?>
                                >
                                <?php echo esc_html($option); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?php $placeholderAttr = $field->placeholder ? ' placeholder="' . esc_attr($field->placeholder) . '"' : ''; ?>
                    <input
                        type="<?php echo esc_attr($field->type); ?>"
                        id="<?php echo esc_attr($field->name); ?>"
                        name="<?php echo esc_attr($field->name); ?>"
                        <?php echo $field->required ? 'required' : ''; ?>
                        <?php echo $placeholderAttr; ?>
                    >
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>

        <button type="submit">
            <?php echo esc_html($form->submitButtonText ?? 'Enviar'); ?>
        </button>
    </form>
</div>
