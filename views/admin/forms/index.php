<div class="wrap wjm-forms">
    <h1 class="wp-heading-inline">Formulários</h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=wjm_form_editor')); ?>" class="page-title-action">Adicionar Novo</a>
    
    <hr class="wp-header-end">

    <div class="wjm-form-editor-container">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>E-mail de Destino</th>
                    <th>Shortcode</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forms as $form): ?>
                <tr>
                    <td><?php echo esc_html((string)$form->id); ?></td>
                    <td><?php echo esc_html($form->title); ?></td>
                    <td><?php echo esc_html($form->recipientEmail); ?></td>
                    <td><code>[wjm_form id="<?php echo esc_attr((string)$form->id); ?>"]</code></td>
                    <td>
                        <div class="wjm-editor-actions">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wjm_form_editor&id=' . $form->id)); ?>" class="button button-small">
                                <span class="dashicons dashicons-edit"></span> Editar
                            </a>
                            <a href="<?php echo esc_url(
                                    wp_nonce_url(
                                        admin_url('admin-post.php?action=wjm_delete_form&id=' . $form->id),
                                        'wjm_delete_form_' . $form->id
                                    )
                                ); ?>"
                                class="button button-small wjm-remove-field" style="color:white;background-color:#dc3232;"
                                onclick="return confirm('Tem certeza que deseja excluir este formulário?');">
                                    <span class="dashicons dashicons-trash"></span> Excluir
                            </a>
                            <button class="button button-small wjm-copy-shortcode" data-shortcode='[wjm_form id="<?php echo esc_attr((string)$form->id); ?>"]'>
                                <span class="dashicons dashicons-clipboard"></span> Copiar
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>