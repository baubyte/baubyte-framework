<?php

namespace Baubyte\Validation\Rules;

class Number implements ValidationRule {
    /**
     * @inheritDoc
     */
    public function message(): string {
        return "Este Campo debe ser Numérico.";
    }

    /**
     * @inheritDoc
     */
    public function isValid(string $field, array $data): bool {
        return isset($data[$field]) && is_numeric($data[$field]);
    }
}
