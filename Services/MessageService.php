<?php
namespace Services;

use Repositories\MessageRepository;

class MessageService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new MessageRepository();
    }

    public function getMessages($startDate = null, $endDate = null, $formId = null)
    {
        return $this->repository->fetchMessages($startDate, $endDate, $formId);
    }
}
