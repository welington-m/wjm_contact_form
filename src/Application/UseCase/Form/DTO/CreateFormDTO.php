<?php

namespace WJM\Application\UseCase\Form\DTO;

final class CreateFormDTO
{
    public readonly string $title;
    public readonly array $fields;
    public readonly ?string $recipientEmail;
    public readonly string $submitButtonText;
    public readonly string $errorMessage;
    public readonly string $successMessage;

    public function __construct(
        string $title,
        array $fields,
        ?string $recipientEmail,
        ?string $submitButtonText,
        ?string $errorMessage,
        ?string $successMessage
    ) {
        $this->title = trim($title) !== '' ? $title : 'Formulário sem nome';
        $this->fields = is_array($fields) ? $fields : [];

        $this->recipientEmail = $recipientEmail;
        $this->submitButtonText = $submitButtonText ?? 'Enviar';
        $this->errorMessage = $errorMessage ?? 'Ocorreu um erro ao enviar o formulário. Tente novamente.';
        $this->successMessage = $successMessage ?? 'Seu formulário foi enviado com sucesso!';

        if (!is_array($this->fields)) {
            throw new \InvalidArgumentException("O campo 'fields' deve ser um array.");
        }
    }
}
