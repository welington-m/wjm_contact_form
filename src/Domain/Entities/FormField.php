<?php

namespace WJM\Domain\Entities;

class FormField
{
    public function __construct(
        public string $name,
        public string $label,
        public string $type,
        public bool $required = false,
        public array $options = [] // para selects, radios, checkboxes
    ) {}
}
