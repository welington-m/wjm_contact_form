<?php

namespace WJM\Application\UseCase\Form\DTO;

final class SubmissionDTO
{
    public readonly int $formId;
    public readonly array $data;

    public function __construct(int $formId, array $data)
    {
        if ($formId <= 0) {
            throw new \InvalidArgumentException("Formulário inválido.");
        }

        $this->formId = $formId;
        $this->data = $data;
    }
}
