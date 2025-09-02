<?php

namespace WJM\Application\UseCase\Form;

use WJM\Application\UseCase\Form\DTO\CreateFormDTO;
use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Domain\Entities\Form;
use WJM\Domain\Factories\FormFactory;

final class CreateFormUseCase
{
    private FormRepositoryInterface $formRepository;

    public function __construct(FormRepositoryInterface $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function execute(CreateFormDTO $dto): int
    {
        $form = FormFactory::fromCreateDTO($dto);
        return $this->formRepository->save($form);
    }
}
