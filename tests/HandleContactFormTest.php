<?php
use PHPUnit\Framework\TestCase;
use UseCases\HandleContactForm;

class HandleContactFormTest extends TestCase {
    public function testExecuteRunsWithoutException() {
        $handler = new HandleContactForm();

        $this->expectNotToPerformAssertions();

        $handler->execute([
            'name' => 'Welington',
            'email' => 'welington@example.com',
            'message' => 'Olá!'
        ]);
    }
}