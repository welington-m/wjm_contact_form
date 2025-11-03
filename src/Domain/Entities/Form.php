<?php

namespace WJM\Domain\Entities;

class Form
{
    public function __construct(
        public readonly ?int $id,
        public string $title,
        public int $layout = 1,
        /** @var FormField[] */
        public array $fields = [],
        public ?string $recipientEmail = null,
        public ?string $submitButtonText = 'Enviar',
        public ?string $errorMessage = 'Erro ao enviar.',
        public ?string $successMessage = 'Mensagem enviada com sucesso.'
    ) {}
}
