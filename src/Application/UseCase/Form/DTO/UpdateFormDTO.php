<?php

namespace WJM\Application\UseCase\Form\DTO;

final class UpdateFormDTO
{
    /**
     * @param int $id ID do formulário
     * @param string $title Título do formulário
     * @param array $fields Campos do formulário (espera um array associativo para o factory criar FormField)
     * @param string|null $recipientEmail E-mail destinatário
     * @param string $submitButtonText Texto do botão de envio
     * @param string $errorMessage Mensagem de erro
     * @param string $successMessage Mensagem de sucesso
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly array $fields,
        public readonly ?string $recipientEmail,
        public readonly string $submitButtonText,
        public readonly string $errorMessage,
        public readonly string $successMessage
    ) {
        
        if ($this->id <= 0) {
            throw new \InvalidArgumentException("ID do formulário inválido.");
        }

        if (trim($this->title) === '') {
            throw new \InvalidArgumentException("Título do formulário não pode ser vazio.");
        }

        if (!is_array($this->fields)) {
            throw new \InvalidArgumentException("Campos do formulário devem ser um array.");
        }
    }
}
