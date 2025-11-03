<?php

namespace WJM\Infra\Repositories;

use WJM\Domain\Entities\Form;
use WJM\Domain\Entities\FormField;
use WJM\Domain\Entities\FormMessage;
use WJM\Domain\Repositories\FormRepositoryInterface;
use wpdb;

class FormRepository implements FormRepositoryInterface
{
    private string $formsTable;
    private string $messagesTable;

    public function __construct(private wpdb $db)
    {
        $this->formsTable = $this->db->prefix . 'wjm_forms';
        $this->messagesTable = $this->db->prefix . 'wjm_form_messages';
    }

    public function findById(int $id): ?Form
    {
        $row = $this->db->get_row(
            $this->db->prepare("SELECT * FROM {$this->formsTable} WHERE id = %d", $id),
            ARRAY_A
        );

        if (!$row) return null;

        return $this->mapRowToForm($row);
    }

    public function findAll(): array
    {
        $rows = $this->db->get_results("SELECT * FROM {$this->formsTable}", ARRAY_A);

        return array_map([$this, 'mapRowToForm'], $rows);
    }

    public function save(Form $form): int
    {
        $data = [
            'title' => $form->title,
            'layout' => (int) $form->layout,
            'fields' => json_encode(array_map(function (FormField $field) {
                return [
                    'name' => $field->name,
                    'label' => $field->label,
                    'type' => $field->type,
                    'required' => $field->required,
                    'options' => $field->options,
                    'column' => $field->column,
                    'fullWidth' => $field->fullWidth,
                    'placeholder' => $field->placeholder,
                ];
            }, $form->fields)),
            'recipient_email' => $form->recipientEmail,
            'submit_button_text' => $form->submitButtonText,
            'error_message' => $form->errorMessage,
            'success_message' => $form->successMessage,
            'updated_at' => current_time('mysql'),
        ];

        if ($form->id > 0) {
            $this->db->update($this->formsTable, $data, ['id' => $form->id]);
            return $form->id;
        } else {
            $data['created_at'] = current_time('mysql');
            $this->db->insert($this->formsTable, $data);
            return (int) $this->db->insert_id;
        }
    }

    public function delete(int $formId): bool
    {
        return (bool) $this->db->delete($this->formsTable, ['id' => $formId]);
    }

    /**
     * Transforma um row do banco em uma entidade Form
     */
    private function mapRowToForm(array $row): Form
    {
        $fields = json_decode($row['fields'], true) ?? [];
        $formFields = array_map(function ($f) {
            return new FormField(
                name: $f['name'] ?? '',
                label: $f['label'] ?? '',
                type: $f['type'] ?? 'text',
                required: $f['required'] ?? false,
                options: $f['options'] ?? [],
                column: isset($f['column']) ? (int) $f['column'] : 1,
                fullWidth: $f['fullWidth'] ?? false,
                placeholder: $f['placeholder'] ?? null
            );
        }, $fields);

        return new Form(
            id: (int) $row['id'],
            title: $row['title'],
            layout: isset($row['layout']) ? (int) $row['layout'] : 1,
            fields: $formFields,
            recipientEmail: $row['recipient_email'],
            submitButtonText: $row['submit_button_text'],
            errorMessage: $row['error_message'],
            successMessage: $row['success_message']
        );
    }
}
