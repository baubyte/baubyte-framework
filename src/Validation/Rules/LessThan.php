<?php

namespace Baubyte\Validation\Rules;

class LessThan implements ValidationRule {
    /**
     * Field that must be present when field under validation is present.
     */
    protected float $lessThan;

    /**
     * Instantiate required less than rule.
     *
     * @param string $lessThan field to check when validating actual field.
     */
    public function __construct(float $lessThan) {
        $this->lessThan = $lessThan;
    }

    /**
     * @inheritDoc
     */
    public function message(): string {
        return "Debe ser un valor numérico inferior a {$this->lessThan}.";
    }

    /**
     * @inheritDoc
     */
    public function isValid(string $field, array $data): bool {
        return isset($data[$field])
            && is_numeric($data[$field])
            && $data[$field] < $this->lessThan;
    }
}
