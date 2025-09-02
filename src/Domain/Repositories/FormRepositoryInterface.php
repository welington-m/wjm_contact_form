<?php
namespace WJM\Domain\Repositories;

use WJM\Domain\Entities\Form;

interface FormRepositoryInterface {
    public function findById(int $id): ?Form;
    public function save(Form $form): int;
    public function findAll(): array;
    public function delete(int $id): bool;
}
