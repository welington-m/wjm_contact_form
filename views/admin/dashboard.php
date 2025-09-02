<div class="wrap">
    <h1>📊 Painel de Controle - WJM Contact Form</h1>

    <div style="margin: 20px 0;">
        <a href="<?= esc_url($new_form_url) ?>" class="button button-primary">➕ Novo Formulário</a>
        <a href="<?= esc_url($messages_url) ?>" class="button">📨 Ver Mensagens</a>
    </div>

    <h2>📝 Formulários Cadastrados</h2>

    <?php if (empty($forms)) : ?>
        <p>Nenhum formulário encontrado.</p>
    <?php else : ?>
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Shortcode</th>
                    <th>Criado em</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($forms as $form) : ?>
                    <tr>
                        <td><?php echo esc_html((string)$form->id); ?></td>
                        <td><?php echo esc_html($form->title); ?></td>
                        <td><?php echo esc_html($form->recipientEmail); ?></td>
                        <td><code>[wjm_form id="<?php echo esc_attr((string)$form->id); ?>"]</code></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=wjm_form_editor&id=' . $form->id)); ?>" class="button button-small">Editar</a>
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
                            <button class="button button-small" onclick="navigator.clipboard.writeText('[wjm_form id=<?php echo esc_js((string)$form->id); ?>]')">Copiar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div style="margin-top: 40px;">
        <img src="<?= esc_url($banner_url) ?>" alt="WJM Banner" style="max-width: 470px; height: auto;" />
    </div>
</div>
