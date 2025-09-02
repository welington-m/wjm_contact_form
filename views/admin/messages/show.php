<div class="wrap">
    <h1>Detalhes da Mensagem #<?= esc_html($message->getId()) ?></h1>
    
    <a href="<?= esc_url($back_url) ?>" class="button">← Voltar para a lista</a>
    
    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>Informações Básicas</h2>
        </div>
        <div class="card-body">
            <table class="widefat fixed">
                <tr>
                    <th width="200">Formulário:</th>
                    <td><?= esc_html($message->getFormTitle()) ?></td>
                </tr>
                <tr>
                    <th>Data de Envio:</th>
                    <td><?= esc_html($message->getSubmittedAt()) ?></td>
                </tr>
                <tr>
                    <th>Endereço IP:</th>
                    <td><?= esc_html($message->ipAddress) ?></td>
                </tr>
                <tr>
                    <th>Visualizada:</th>
                    <td><?= $message->viewed ? 'Sim' : 'Não' ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <div class="card-header">
            <h2>Dados do Formulário</h2>
        </div>
        <div class="card-body">
            <table class="widefat fixed">
                <?php foreach ($message->getData() as $key => $value): ?>
                <tr>
                    <th width="200"><?= esc_html(ucfirst(str_replace('_', ' ', $key))) ?>:</th>
                    <td><?= esc_html(is_array($value) ? implode(', ', $value) : $value) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>