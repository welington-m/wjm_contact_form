<?php

namespace WJM\Domain\Entities;

class FormMessage
{
    public function __construct(
        public ?int $id,
        public int $formId,
        public array $data,
        public \DateTimeImmutable $submittedAt,
        public string $ipAddress,
        public bool $viewed = false
    ) {}

    public function toHtml(): string
    {
        $html = '<h2>Nova mensagem recebida</h2>';
        $html .= '<ul>';

        foreach ($this->data as $field => $value) {
            $escapedField = htmlspecialchars(ucfirst(str_replace('_', ' ', $field)), ENT_QUOTES, 'UTF-8');
            $escapedValue = is_array($value)
                ? implode(', ', array_map('htmlspecialchars', $value))
                : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            $html .= "<li><strong>{$escapedField}:</strong> {$escapedValue}</li>";
        }

        $html .= '</ul>';
        $html .= '<p><strong>IP do remetente:</strong> ' . htmlspecialchars($this->ipAddress, ENT_QUOTES, 'UTF-8') . '</p>';

        return $html;
    }
    public function getId(): int
    {
        return $this->id ?? 0;
    }

    public function getFormTitle(): string
    {
        // Você precisará injetar o FormRepository ou os dados do formulário
        return 'Formulário #' . $this->formId; // Temporário
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getSubmittedAt(): string
    {
        return $this->submittedAt->format('d/m/Y H:i');
    }
}
