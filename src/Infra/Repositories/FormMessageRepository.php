<?php

namespace WJM\Infra\Repositories;

use wpdb;
use WJM\Domain\Entities\FormMessage;
use WJM\Domain\Repositories\FormMessageRepositoryInterface;

class FormMessageRepository implements FormMessageRepositoryInterface
{
    public function __construct(private wpdb $wpdb) {}

    public function save(FormMessage $message): bool
    {
        $data = [
            'form_id' => $message->formId,
            'data' => json_encode($message->data),
            'submitted_at' => $message->submittedAt->format('Y-m-d H:i:s'),
            'ip_address' => $message->ipAddress,
            'viewed' => (int) $message->viewed
        ];

        return (bool) $this->wpdb->insert(
            $this->wpdb->prefix . 'wjm_form_messages',
            $data
        );
    }

    public function findById(int $id): ?FormMessage
    {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}wjm_form_messages 
                WHERE id = %d",
                $id
            )
        );

        if (!$row) {
            return null;
        }

        return $this->mapToEntity($row);
    }

    public function getMessages(string $search = '', int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM {$this->wpdb->prefix}wjm_form_messages";
        $params = [];

        if ($search !== '') {
            $query .= " WHERE data LIKE %s";
            $params[] = '%' . $this->wpdb->esc_like($search) . '%';
        }

        $query .= " ORDER BY submitted_at DESC LIMIT %d OFFSET %d";
        $params[] = $perPage;
        $params[] = $offset;

        $results = $this->wpdb->get_results(
            $this->wpdb->prepare($query, ...$params)
        );

        return array_map([$this, 'mapToEntity'], $results);
    }

    public function countMessages(string $search = ''): int
    {
        $query = "SELECT COUNT(*) FROM {$this->wpdb->prefix}wjm_form_messages";
        
        if ($search !== '') {
            $query .= " WHERE data LIKE %s";
            return (int) $this->wpdb->get_var(
                $this->wpdb->prepare($query, '%' . $this->wpdb->esc_like($search) . '%')
            );
        }

        return (int) $this->wpdb->get_var($query);
    }

    public function markAsViewed(int $id, string $username): bool
    {
        return (bool) $this->wpdb->update(
            $this->wpdb->prefix . 'wjm_form_messages',
            [
                'viewed' => 1,
                'viewed_by' => $username,
                'viewed_at' => current_time('mysql')
            ],
            ['id' => $id],
            ['%d', '%s', '%s'],
            ['%d']
        );
    }

    public function getMessagesForExport(array $filters = []): array
    {
        $query = "SELECT * FROM {$this->wpdb->prefix}wjm_form_messages";
        $where = [];
        $params = [];

        // Filtro por ID do formulário
        if (!empty($filters['form_id'])) {
            $where[] = "form_id = %d";
            $params[] = $filters['form_id'];
        }

        // Filtro por texto
        if (!empty($filters['search'])) {
            $where[] = "data LIKE %s";
            $params[] = '%' . $this->wpdb->esc_like($filters['search']) . '%';
        }

        // Filtro por data
        if (!empty($filters['date_from'])) {
            $where[] = "submitted_at >= %s";
            $params[] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $where[] = "submitted_at <= %s";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        // Filtro por status de visualização
        if (isset($filters['viewed']) && $filters['viewed'] !== null) {
            $where[] = "viewed = %d";
            $params[] = (int)$filters['viewed'];
        }

        // Monta a query
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $query .= " ORDER BY submitted_at DESC";

        // Executa a query
        if (!empty($params)) {
            $query = $this->wpdb->prepare($query, $params);
        }

        $results = $this->wpdb->get_results($query);

        return array_map([$this, 'mapToEntity'], $results);
    }

    private function mapToEntity(object $row): FormMessage
    {
        return new FormMessage(
            id: (int)$row->id,
            formId: (int)$row->form_id,
            data: json_decode($row->data, true) ?: [],
            submittedAt: new \DateTimeImmutable($row->submitted_at),
            ipAddress: $row->ip_address,
            viewed: (bool)$row->viewed
        );
    }
}