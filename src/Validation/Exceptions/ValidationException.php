<?php

namespace Baubyte\Validation\Exceptions;

use Baubyte\Exceptions\BaubyteException;

class ValidationException extends BaubyteException {
    /**
     * Errors Validations
     *
     * @var array
     */
    protected array $errors;

    /**
     * Instantiate required Validation Exception.
     *
     * @param array $errors
     */
    public function __construct(array $errors) {
        $this->errors = $errors;
    }


    /**
     * Get errors Validations
     *
     * @return  array
     */
    public function errors(): array {
        return $this->errors;
    }
}
