<?php

namespace WJM\Application\UseCase\Form;

use WJM\Application\Mappers\FormDTOMapper;
use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Application\UseCase\Form\DTO\UpdateFormDTO;

final class GetFormUseCase
{
    public function __construct(
        private FormRepositoryInterface $formRepository
    ) {}

    public function execute(int $id): ?UpdateFormDTO
    {
        $form = $this->formRepository->findById($id);

        return $form ? FormDTOMapper::toUpdateDTO($form) : null;
    }

}