<?php
namespace Repositories;

use Entities\FormData;

class FormStorage {
    public function save(FormData $data): void {
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix . 'wjm_contact_form',
            [
                'name' => $data->name,
                'email' => $data->email->get(),
                'message' => $data->message,
                'topic' => $data->topic,
                'created_at' => current_time('mysql')
            ]
        );
    }
}