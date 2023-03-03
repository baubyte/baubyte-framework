<?php

namespace Baubyte\Validation\Rules;

class RequiredWith implements ValidationRule {
    /**
     * Field that must be present when field under validation is present.
     */
    protected string $withField;

    /**
     * Instantiate required with rule.
     *
     * @param string $withField field to check when validating actual field.
     */
    public function __construct(string $withField) {
        $this->withField = $withField;
    }

    /**
     * @inheritDoc
     */
    public function message(): string {
        return "Este Campo es Obligatorio cuando {$this->withField} está presente.";
    }

    /**
     * @inheritDoc
     */
    public function isValid(string $field, array $data): bool {
        if (isset($this->withField) && $this->withField !== "") {
            return isset($data[$field]) && $data[$field] !== "";
        }

        return true;
    }
}
