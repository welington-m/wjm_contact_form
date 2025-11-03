<?php

namespace WJM\Domain\Repositories;

use WJM\Domain\Entities\FormMessage;

interface FormMessageRepositoryInterface
{
    public function save(FormMessage $message): bool;
    
    public function findById(int $id): ?FormMessage;
    
    public function getMessages(string $search = '', int $page = 1, int $perPage = 10): array;
    
    public function countMessages(string $search = ''): int;
    
    public function markAsViewed(int $id, string $username): bool;
    
    public function getMessagesForExport(array $filters = []): array;
}