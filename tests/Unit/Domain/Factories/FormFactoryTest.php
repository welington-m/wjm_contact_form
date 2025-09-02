<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Factories;

use PHPUnit\Framework\TestCase;
use WJM\Domain\Factories\FormFactory;
use WJM\Application\UseCase\Form\DTO\CreateFormDTO;
use WJM\Domain\Entities\FormField;

class FormFactoryTest extends TestCase
{
    public function testShouldMapOptionsFromDto(): void
    {
        $dto = new CreateFormDTO(
            title: 'Teste',
            fields: [
                [
                    'name' => 'selecionar',
                    'type' => 'select',
                    'label' => 'Selecionar',
                    'required' => false,
                    'options' => ['A', 'B']
                ]
            ],
            recipientEmail: 'test@example.com',
            submitButtonText: 'Enviar',
            errorMessage: 'Erro',
            successMessage: 'Sucesso'
        );

        $form = FormFactory::fromCreateDTO($dto);
        $field = $form->fields[0];

        $this->assertInstanceOf(FormField::class, $field);
        $this->assertSame(['A', 'B'], $field->options);
    }
}

