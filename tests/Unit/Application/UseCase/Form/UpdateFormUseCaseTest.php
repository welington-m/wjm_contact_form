<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase\Form;

use PHPUnit\Framework\TestCase;
use WJM\Application\UseCase\Form\DTO\UpdateFormDTO;
use WJM\Application\UseCase\Form\UpdateFormUseCase;
use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Domain\Entities\Form;
use WJM\Domain\Factories\FormFactory;

class UpdateFormUseCaseTest extends TestCase
{
    public function testShouldUpdateFormCorrectly(): void
    {
        $dto = new UpdateFormDTO(
            id: 99,
            title: 'Formulário Atualizado',
            fields: [['name' => 'nome', 'type' => 'text']],
            recipientEmail: 'novo@email.com',
            submitButtonText: 'Atualizar',
            errorMessage: 'Falha ao atualizar',
            successMessage: 'Atualizado com sucesso',
            layout: 2
        );

        $repositoryMock = $this->createMock(FormRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Form $form) use ($dto) {
                return $form->id === $dto->id && $form->layout === $dto->layout;
            }));

        $useCase = new UpdateFormUseCase($repositoryMock, new FormFactory());
        $useCase->execute($dto);

        $this->assertTrue(true);
    }
}
