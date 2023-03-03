<?php

namespace Baubyte\Validation\Rules;

interface ValidationRule {
    /**
     * Default message to display when validation fails.
     *
     * @return string
     */
    public function message(): string;
    /**
     * Check if given data passes validation.
     *
     * @param string $field Field under validation.
     * @param array &$data Reference to data under validation.
     * @return bool
     */
    public function isValid(string $field, array $data): bool;
}
