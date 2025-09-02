<?php

namespace WJM\Application\UseCase\Form;

use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Domain\Entities\Form;

final class ListFormsUseCase
{
    public function __construct(
        private FormRepositoryInterface $formRepository
    ) {}

    /**
     * @return Form[]
     */
    public function execute(): array
    {
        return $this->formRepository->findAll();
    }
}