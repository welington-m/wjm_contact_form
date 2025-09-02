<?php
declare(strict_types=1);

/** @var \WJM\Domain\Entities\Form[] $forms */

?>

<h1 class="wp-heading-inline">📝 Formulários Criados</h1>
<a href="<?php echo esc_url(admin_url('admin.php?page=wjm_form_editor')); ?>" class="page-title-action">
    Novo Formulário
</a>
<hr class="wp-header-end">

<?php if (empty($forms)) : ?>
    <p>Nenhum formulário cadastrado ainda.</p>
<?php else : ?>
    <table class="wp-list-table widefat fixed striped table-view-list">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Destinatário</th>
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
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wjm_form_editor&id=' . $form->id)); ?>" class="button button-small">Editar</a>
                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=wjm_forms&action=delete&id=' . $form->id), 'wjm_delete_form_' . $form->id)); ?>"
                       class="button button-small wjm-delete-form" style="color:red;"
                       onclick="return confirm('Tem certeza que deseja excluir este formulário?');">
                        Excluir
                    </a>
                    <button class="button button-small" onclick="navigator.clipboard.writeText('[wjm_form id=<?php echo esc_js((string)$form->id); ?>]')">Copiar</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
