<?php

declare(strict_types=1);

namespace Tests\Unit\Application\UseCase\Form;

use PHPUnit\Framework\TestCase;
use WJM\Application\UseCase\Form\CreateFormUseCase;
use WJM\Application\UseCase\Form\DTO\CreateFormDTO;
use WJM\Domain\Factories\FormFactory;
use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Domain\Entities\Form;

class CreateFormUseCaseTest extends TestCase
{
    public function testShouldCreateFormCorrectly(): void
    {
        // Arrange: cria DTO simulado
        $dto = new CreateFormDTO(
            title: 'Contato Rápido',
            fields: [
                ['name' => 'email', 'type' => 'email'],
                ['name' => 'mensagem', 'type' => 'textarea']
            ],
            recipientEmail: 'contato@empresa.com',
            submitButtonText: 'Enviar Mensagem',
            errorMessage: 'Erro ao enviar.',
            successMessage: 'Mensagem enviada com sucesso.'
        );

        // Mock do repositório
        $formRepositoryMock = $this->createMock(FormRepositoryInterface::class);

        // Espera que o método save seja chamado 1x com um objeto Form
        $formRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Form $form) use ($dto) {
                return $form->title === $dto->title
                    && count($form->fields) === count($dto->fields)
                    && $form->recipientEmail === $dto->recipientEmail;
            }))
            ->willReturn(99); // simula ID retornado

        // Act
        $useCase = new CreateFormUseCase($formRepositoryMock, new FormFactory());
        $createdId = $useCase->execute($dto);

        // Assert
        $this->assertEquals(99, $createdId);
    }
}
