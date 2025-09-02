<?php

namespace WJM\Application\UseCase\Form;

use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Domain\Factories\FormFactory;
use WJM\Application\UseCase\Form\DTO\UpdateFormDTO;

final class UpdateFormUseCase
{
    public function __construct(
        private FormRepositoryInterface $formRepository,
        private FormFactory $formFactory
    ) {}

    public function execute(UpdateFormDTO $dto): void
    {
        $form = $this->formFactory->fromUpdateDTO($dto);
        $this->formRepository->save($form);
    }
}