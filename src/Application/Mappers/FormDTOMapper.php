<?php

namespace WJM\Application\Mappers;

use WJM\Application\UseCase\Form\DTO\UpdateFormDTO;
use WJM\Domain\Entities\Form;

class FormDTOMapper
{
    public static function toUpdateDTO(Form $form): UpdateFormDTO
    {
        return new UpdateFormDTO(
            id: $form->id,
            title: $form->title,
            fields: array_map(function ($field) {
                return [
                    'name' => $field->name,
                    'type' => $field->type,
                    'label' => $field->label,
                    'required' => $field->required,
                ];
            }, $form->fields),
            recipientEmail: $form->recipientEmail,
            submitButtonText: $form->submitButtonText,
            errorMessage: $form->errorMessage,
            successMessage: $form->successMessage
        );
    }
}
