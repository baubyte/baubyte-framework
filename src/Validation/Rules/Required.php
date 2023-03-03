<?php

namespace Baubyte\Validation\Rules;

class Required implements ValidationRule {
    /**
     * @inheritDoc
     */
    public function message(): string {
        return "Este Campo es Obligatorio.";
    }

    /**
     * @inheritDoc
     */
    public function isValid(string $field, array $data): bool {
        return isset($data[$field]) && trim($data[$field]) !== "";
    }
}
