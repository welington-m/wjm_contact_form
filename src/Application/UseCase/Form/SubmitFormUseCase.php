<?php

namespace WJM\Application\UseCase\Form;

use WJM\Application\UseCase\Form\DTO\SubmissionDTO;
use WJM\Domain\Repositories\FormRepositoryInterface;
use WJM\Domain\Repositories\FormMessageRepositoryInterface;
use WJM\Domain\Factories\FormMessageFactory;
use WJM\Infra\Services\EmailSenderInterface;

final class SubmitFormUseCase
{
    public function __construct(
        private FormRepositoryInterface $formRepository,
        private FormMessageRepositoryInterface $messageRepository,
        private EmailSenderInterface $emailSender,
        private FormMessageFactory $messageFactory
    ) {}

    public function execute(SubmissionDTO $dto): array
    {
        $form = $this->formRepository->findById($dto->formId);

        if (!$form) {
            return ['success' => false, 'error' => 'Formulário não encontrado'];
        }

        $message = $this->messageFactory->fromDTO($dto, $form);

        try {
            $saved = $this->messageRepository->save($message);

            if (!$saved) {
                return [
                    'success' => false,
                    'error' => $form->errorMessage ?? 'Falha ao salvar mensagem',
                ];
            }

            $emailSent = $this->emailSender->send(
                to: $form->recipientEmail,
                subject: "Nova mensagem do formulário: {$form->title}",
                body: $message->toHtml()
            );

            if (!$emailSent) {
                return [
                    'success' => false,
                    'error' => $form->errorMessage ?? 'Falha ao enviar o e-mail.',
                ];
            }

            return [
                'success' => true,
                'message' => $form->successMessage ?? 'Formulário enviado com sucesso!',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $form->errorMessage ?? 'Ocorreu um erro ao enviar o formulário.',
            ];
        }
    }
}
