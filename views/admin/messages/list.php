<?php
/** @var array $messages Lista de mensagens (instâncias de WJM\Domain\Entities\FormMessage) */
/** @var int $total Total de mensagens */
/** @var int $currentPage Página atual */
/** @var int $perPage Itens por página */
/** @var string $searchTerm Termo de busca */
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Mensagens Recebidas</h1>
    <a href="<?= esc_url(add_query_arg(['page' => 'wjm_form_messages', 'export' => 'all'], admin_url('admin.php'))) ?>" class="page-title-action">
        Exportar Tudo
    </a>
    <hr class="wp-header-end">

    <!-- Filtro de busca -->
    <form method="get" action="">
        <input type="hidden" name="page" value="wjm_form_messages">
        <p class="search-box">
            <label class="screen-reader-text" for="message-search-input">Buscar Mensagens:</label>
            <input type="search" id="message-search-input" name="s" value="<?php echo esc_attr($searchTerm); ?>">
            <input type="submit" class="button" value="Buscar">
            <?php if (!empty($searchTerm)): ?>
                <a href="<?= esc_url(remove_query_arg('s')) ?>" class="button">Limpar</a>
            <?php endif; ?>
        </p>
    </form>

    <!-- Tabela de mensagens -->
    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Formulário</th>
                <th>Remetente</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($messages)): ?>
            <tr>
                <td colspan="5">Nenhuma mensagem encontrada.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= esc_html($msg->getId()) ?></td>
                    <td><?= esc_html($msg->getFormTitle()) ?></td>
                    <td>
                        <?php 
                        $data = $msg->getData();
                        echo esc_html($data['nome'] ?? $data['email'] ?? 'N/A'); 
                        ?>
                    </td>
                    <td><?= esc_html($msg->getSubmittedAt()) ?></td>
                    <td>
                        <a href="<?= esc_url(
                            add_query_arg(
                                [
                                    'page' => 'wjm_view_message',
                                    'id' => $msg->getId()
                                ],
                                admin_url('admin.php')
                            )
                        ) ?>" class="button">
                            Ver
                        </a>
                        <a href="<?= esc_url(
                            add_query_arg(
                                [
                                    'page' => 'wjm_form_messages',
                                    'export' => $msg->getId()
                                ],
                                admin_url('admin.php')
                            )
                        ) ?>" class="button">
                            Exportar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Paginação -->
    <?php if ($total > $perPage): ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                $totalPages = ceil($total / $perPage);
                $baseUrl = add_query_arg(['page' => 'wjm_form_messages', 's' => $searchTerm], admin_url('admin.php'));
                for ($i = 1; $i <= $totalPages; $i++):
                    $active = $i === $currentPage ? 'class="current-page"' : '';
                    echo '<a ' . $active . ' href="' . esc_url(add_query_arg('paged', $i, $baseUrl)) . '">' . $i . '</a> ';
                endfor;
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>