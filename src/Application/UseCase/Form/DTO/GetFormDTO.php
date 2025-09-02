<?php

namespace WJM\Application\UseCase\Form\DTO;

final class GetFormDTO
{
    public function __construct(
        public readonly int $formId
    ) {}
}