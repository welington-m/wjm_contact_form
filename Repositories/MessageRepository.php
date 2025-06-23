<?php

namespace Repositories;

class MessageRepository
{
    private $wpdb;
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table = $this->wpdb->prefix . 'wjm_form_messages';
    }

    /**
     * Busca mensagens por data e formulário
     *
     * @param int|null $formId
     * @param string|null $startDate (Y-m-d)
     * @param string|null $endDate (Y-m-d)
     * @return array
     */
    public function getMessages($formId = null, $startDate = null, $endDate = null)
    {
        $where = [];
        $params = [];

        if ($formId) {
            $where[] = "form_id = %d";
            $params[] = $formId;
        }

        if ($startDate) {
            $where[] = "created_at >= %s";
            $params[] = $startDate . ' 00:00:00';
        }

        if ($endDate) {
            $where[] = "created_at <= %s";
            $params[] = $endDate . ' 23:59:59';
        }

        $sql = "SELECT * FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY created_at DESC";

        return $this->wpdb->get_results($this->wpdb->prepare($sql, ...$params), ARRAY_A);
    }
}
