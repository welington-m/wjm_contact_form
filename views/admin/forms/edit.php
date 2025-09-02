<div class="wrap wjm-form-editor">
    <h1 class="wp-heading-inline">
        <?php echo $form ? 'Editar Formulário' : 'Adicionar Novo Formulário'; ?>
    </h1>
    
    <hr class="wp-header-end">

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="wjm_save_form">
        <?php if ($form): ?>
            <input type="hidden" name="id" value="<?php echo esc_attr($form->id); ?>">
            <?php wp_nonce_field('wjm_save_form_' . $form->id); ?>
        <?php else: ?>
            <?php wp_nonce_field('wjm_create_form'); ?>
        <?php endif; ?>

        <div class="wjm-form-editor-container">
            <div class="wjm-form-group">
                <h2>Configurações Básicas</h2>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="wjm-form-title">Título do Formulário</label></th>
                            <td>
                                <input type="text" id="wjm-form-title" name="name" class="regular-text" 
                                       value="<?php echo $form ? esc_attr($form->title) : ''; ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wjm-form-recipient">E-mail de Destino</label></th>
                            <td>
                                <input type="email" id="wjm-form-recipient" name="recipient" class="regular-text" 
                                       value="<?php echo $form ? esc_attr($form->recipientEmail) : ''; ?>">
                                <p class="description">Deixe em branco para usar o e-mail padrão do WordPress</p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <h2>Mensagens</h2>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="wjm-form-success">Mensagem de Sucesso</label></th>
                            <td>
                                <textarea id="wjm-form-success" name="success_message" class="regular-text"><?php 
                                    echo $form ? esc_textarea($form->successMessage) : 'Seu formulário foi enviado com sucesso!';
                                ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="wjm-form-error">Mensagem de Erro</label></th>
                            <td>
                                <textarea id="wjm-form-error" name="error_message" class="regular-text"><?php 
                                    echo $form ? esc_textarea($form->errorMessage) : 'Ocorreu um erro ao enviar o formulário. Tente novamente.';
                                ?></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="wjm-form-group">
                <h2>Campos do Formulário</h2>

                <p class="wjm-submit-button-text">
                    <label for="wjm-submit-button-text">Texto do Botão Enviar</label><br>
                    <input type="text" id="wjm-submit-button-text" name="submit_button_text" class="regular-text" value="<?php echo $form ? esc_attr($form->submitButtonText) : 'Enviar'; ?>">
                </p>

                <button type="button" id="wjm-toggle-editor" class="button">
                    <span class="dashicons dashicons-edit"></span>
                    <span class="btn-text">Mostrar Editor Visual</span>
                </button>

                <div id="wjm-visual-editor">
                    <div id="wjm-fields-container" class="wjm-fields-container">
                        <?php if ($form && !empty($form->fields)): ?>
                            <?php foreach ($form->fields as $index => $field): ?>
                                <?php if (!is_array($field)) continue; ?>
                                <div class="wjm-field-card" data-index="<?php echo $index; ?>">
                                    <div class="wjm-field-header">
                                        <h3>Campo #<?php echo $index + 1; ?></h3>
                                        <button type="button" class="button button-secondary wjm-remove-field">
                                            <span class="dashicons dashicons-trash"></span> Remover
                                        </button>
                                    </div>
                                    <div class="wjm-field-body">
                                        <div class="wjm-field-row">
                                            <label>Nome: 
                                                <input type="text" class="wjm-field-name" placeholder="Ex: email" 
                                                       value="<?php echo esc_attr($field['name'] ?? ''); ?>" required>
                                            </label>
                                        </div>
                                        <div class="wjm-field-row">
                                            <label>Rótulo: 
                                                <input type="text" class="wjm-field-label" placeholder="Ex: Seu Email" 
                                                       value="<?php echo esc_attr($field['label']); ?>" required>
                                            </label>
                                        </div>
                                        <div class="wjm-field-row">
                                            <label>Tipo:
                                                <select class="wjm-field-type">
                                                    <option value="text" <?php selected($field['type'], 'text'); ?>>Texto</option>
                                                    <option value="email" <?php selected($field['type'], 'email'); ?>>Email</option>
                                                    <option value="textarea" <?php selected($field['type'], 'textarea'); ?>>Textarea</option>
                                                    <option value="select" <?php selected($field['type'], 'select'); ?>>Select</option>
                                                    <option value="radio" <?php selected($field['type'], 'radio'); ?>>Radio</option>
                                                    <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>>Checkbox</option>
                                                </select> <!-- Tipos adicionais suportados -->
                                            </label>
                                        </div>
                                        <div class="wjm-field-row">
                                            <label>
                                                <input type="checkbox" class="wjm-field-required" <?php checked($field['required']); ?>>
                                                Obrigatório
                                            </label>
                                        </div>
                                        <!-- Bloco de opções para campos select/radio/checkbox -->
                                        <div class="wjm-field-options" style="<?php echo in_array(($field['type'] ?? ''), ['select','radio','checkbox']) ? '' : 'display:none;'; ?>">
                                            <label>Opções:</label>
                                            <div class="wjm-options-list">
                                                <?php if (!empty($field['options']) && is_array($field['options'])): ?>
                                                    <?php foreach ($field['options'] as $option): ?>
                                                        <div class="wjm-option-item">
                                                            <input type="text" class="wjm-field-option" value="<?php echo esc_attr($option); ?>">
                                                            <button type="button" class="button wjm-remove-option">
                                                                <span class="dashicons dashicons-minus"></span>
                                                            </button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            <button type="button" class="button wjm-add-option">
                                                <span class="dashicons dashicons-plus"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="wjm-editor-actions">
                        <button type="button" id="wjm-add-field" class="button">
                            <span class="dashicons dashicons-plus"></span> Adicionar Campo
                        </button>
                        <button type="button" id="wjm-update-json" class="button button-primary">
                            <span class="dashicons dashicons-update"></span> Atualizar JSON
                        </button>
                    </div>
                </div>

                <div class="wjm-json-editor">
                    <label for="wjm_config_json">Configuração JSON:</label>
                    <textarea id="wjm_config_json" name="fields" class="large-text code" rows="10"><?php
                        echo $form ? esc_textarea(json_encode($form->fields, JSON_PRETTY_PRINT)) : '';
                    ?></textarea>
                </div>
            </div>
        </div>

        <div class="wjm-form-submit">
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-yes"></span> Salvar Formulário
            </button>
        </div>
    </form>
</div>