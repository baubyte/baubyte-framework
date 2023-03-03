<?php

namespace Baubyte\Validation\Rules;

class Nullable implements ValidationRule {
    /**
     * @inheritDoc
     */
    public function message(): string {
        return "Este Campo debe estar presente.";
    }

    /**
     * @inheritDoc
     */
    public function isValid(string $field, array $data): bool {
        return array_key_exists($field, $data);
    }
}
