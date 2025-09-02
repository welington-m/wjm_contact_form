<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase\Form;

use PHPUnit\Framework\TestCase;
use WJM\Application\UseCase\Form\DTO\UpdateFormDTO;
use WJM\Application\UseCase\Form\UpdateFormUseCase;
use WJM\Domain\Repositories\FormRepositoryInterface;

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
            successMessage: 'Atualizado com sucesso'
        );

        $repositoryMock = $this->createMock(FormRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('update');

        $useCase = new UpdateFormUseCase($repositoryMock);
        $useCase->execute(99, $dto); // ID 99 como exemplo

        $this->assertTrue(true); // Confirma que executou sem erro
    }
}
