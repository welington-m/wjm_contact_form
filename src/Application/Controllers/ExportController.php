<?php

namespace WJM\Application\Controllers;

use WJM\Domain\Repositories\FormMessageRepositoryInterface;

class ExportController
{
    private FormMessageRepositoryInterface $repository;

    public function __construct(FormMessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function exportMessagesToCSV(): void
    {
        // Verifica permissões
        if (!current_user_can('manage_options')) {
            wp_die(__('Você não tem permissão para executar esta ação.'), 403);
        }

        // Obtém e sanitiza os filtros
        $filters = [
            'form_id' => isset($_GET['form_id']) ? (int)$_GET['form_id'] : null,
            'search' => sanitize_text_field($_GET['s'] ?? ''),
            'date_from' => $this->sanitizeDate($_GET['date_from'] ?? ''),
            'date_to' => $this->sanitizeDate($_GET['date_to'] ?? ''),
            'viewed' => isset($_GET['viewed']) ? (int)$_GET['viewed'] : null
        ];

        // Gera o CSV
        $csvContent = $this->generateCSV($filters);

        // Configura os headers para download
        $this->sendCSVHeaders('mensagens_' . date('Y-m-d_His') . '.csv');

        // Saída do conteúdo
        echo $csvContent;
        exit;
    }

    private function generateCSV(array $filters): string
    {
        $output = fopen('php://temp', 'w');
        
        // Escreve o cabeçalho UTF-8 BOM para Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // Cabeçalhos
        fputcsv($output, [
            'ID',
            'ID do Formulário',
            'Data de Envio',
            'Visualizada',
            'IP',
            'Nome',
            'Email',
            'Mensagem',
            'Dados Completos (JSON)'
        ], ';');

        // Obtém os dados
        $messages = $this->repository->getMessagesForExport($filters);

        // Escreve as linhas
        foreach ($messages as $message) {
            $data = $message->getData();
            fputcsv($output, [
                $message->getId(),
                $message->formId,
                $message->submittedAt->format('Y-m-d H:i:s'),
                $message->viewed ? 'Sim' : 'Não',
                $message->ipAddress,
                $data['nome'] ?? '',
                $data['email'] ?? '',
                $data['mensagem'] ?? '',
                json_encode($data, JSON_UNESCAPED_UNICODE)
            ], ';');
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    private function sendCSVHeaders(string $filename): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    private function sanitizeDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        return date('Y-m-d', strtotime($date)) === $date ? $date : null;
    }
}