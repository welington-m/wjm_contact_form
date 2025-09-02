<?php 

namespace WJM\Infra\Database;

use wpdb;

class FormTableMigrator
{
    public static function migrate(wpdb $db): void
    {
        $charset_collate = $db->get_charset_collate();

        $forms_table = $db->prefix . 'wjm_forms';
        $fields_table = $db->prefix . 'wjm_form_fields';
        $messages_table = $db->prefix . 'wjm_form_messages';

        $sql_forms = "
        CREATE TABLE IF NOT EXISTS {$forms_table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            fields TEXT NOT NULL,
            recipient_email VARCHAR(255),
            submit_button_text VARCHAR(255) DEFAULT 'Enviar',
            error_message TEXT,
            success_message TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) {$charset_collate};
        ";

        $sql_fields = "
        CREATE TABLE IF NOT EXISTS {$fields_table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            form_id BIGINT UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            label VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL,
            required TINYINT(1) DEFAULT 0,
            options TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (form_id)
        ) {$charset_collate};
        ";

        $sql_messages = "
        CREATE TABLE IF NOT EXISTS {$messages_table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            form_id BIGINT UNSIGNED NOT NULL,
            data LONGTEXT NOT NULL,
            submitted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ip_address VARCHAR(45) NOT NULL,
            viewed TINYINT(1) DEFAULT 0 NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (form_id)
        ) {$charset_collate};
        ";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_forms);
        dbDelta($sql_fields);
        dbDelta($sql_messages);
    }
}
