<?php

namespace Baubyte\Validation;

use Baubyte\Validation\Exceptions\RuleParseException;
use Baubyte\Validation\Exceptions\UnknownRuleException;
use Baubyte\Validation\Rules\Email;
use Baubyte\Validation\Rules\LessThan;
use Baubyte\Validation\Rules\Nullable;
use Baubyte\Validation\Rules\Number;
use Baubyte\Validation\Rules\Required;
use Baubyte\Validation\Rules\RequiredWhen;
use Baubyte\Validation\Rules\RequiredWith;
use Baubyte\Validation\Rules\ValidationRule;
use ReflectionClass;

class Rule {
    /**
     * Rule container. Maps rule name to rule class.
     *
     * @var array
     */
    public static array $rules = [];

    /**
     * Baubyte validation rules.
     *
     * @var array
     */
    public static array $defaultRules = [
        Required::class,
        RequiredWith::class,
        RequiredWhen::class,
        Nullable::class,
        Email::class,
        Number::class,
        LessThan::class,
    ];

    /**
     * Default Lune validation rules.
     *
     * @return void
     */
    public static function loadDefaultRules() {
        self::load(self::$defaultRules);
    }

    /**
     * Initialize rules.
     *
     * @param array $rules
     * @return void
     */
    public static function load(array $rules) {
        foreach ($rules as $class) {
            $className = array_slice(explode("\\", $class), -1)[0];
            $ruleName = snake_case($className);
            self::$rules[$ruleName] = $class;
        }
    }

    /**
     * Resolve name of the rule.
     * @param ValidationRule
     * @return string
     */
    public static function nameOf(ValidationRule $rule): string {
        $class = new ReflectionClass($rule);

        return snake_case($class->getShortName());
    }

    /**
     * Get `\Baubyte\Validation\Rules\ValidationRule` associated to `$ruleName`
     * and instantiate it with given `$params`.
     *
     * @param string $ruleName
     * @return ValidationRule
     * @throws RuleParseException
     */
    public static function parseBasicRule(string $ruleName): ValidationRule {
        $class = new ReflectionClass(self::$rules[$ruleName]);
        $constructorParameters = $class->getConstructor()?->getParameters() ?? [];
        if (count($constructorParameters) > 0) {
            throw new RuleParseException("La regla {$ruleName} requiere parámetros.");
        }
        return $class->newInstance();
    }

    /**
     * Get `\Baubyte\Validation\Rules\ValidationRule` associated to `$ruleName`
     * and instantiate it with given `$params`.
     *
     * @param string $ruleName
     * @param string $params
     * @return ValidationRule
     * @throws RuleParseException
     */
    public static function parseRuleWithParameters(string $ruleName, string $params): ValidationRule {
        $class = new ReflectionClass(self::$rules[$ruleName]);
        $constructorParameters = $class->getConstructor()?->getParameters() ?? [];
        $givenParameters = array_filter(explode(',', $params), fn ($param) => !empty($param));

        if (count($givenParameters) !== count($constructorParameters)) {
            throw new RuleParseException(sprintf(
                "La regla %s requiere %d parámetros, pero pasaste %d: %s",
                $ruleName,
                count($constructorParameters),
                count($givenParameters),
                $params
            ));
        }
        return $class->newInstance(...$givenParameters);
    }

    /**
     * Create a new rule object from string format (example: "requiredWith:name").
     *
     * @param string $string
     * @return ValidationRule
     * @throws UnknownRuleException|RuleParseException
     */
    public static function from(string $string): ValidationRule {
        if (strlen($string) === 0) {
            throw new RuleParseException("No se puede hacer un parse de una cadena vacía.");
        }
        $ruleParts = explode(":", $string);

        if (!array_key_exists($ruleParts[0], self::$rules)) {
            throw new UnknownRuleException("La regla {$ruleParts[0]} no se encuentra.");
        }
        if (count($ruleParts) === 1) {
            return self::parseBasicRule($ruleParts[0]);
        }
        [$ruleName, $params] = $ruleParts;
        return self::parseRuleWithParameters($ruleName, $params);
    }

    /**
     * Split rules string by pipe operator.
     *
     * @param mixed $rules
     * @return array
     */
    public static function splitRules(mixed $rules): array {
        if (!is_string($rules)) {
            return [$rules];
        }
        if (strpos($rules, '|') === false) {
            return [$rules];
        }

        $string = $rules;
        $rules  = [];
        $length = strlen($string);
        $cursor = 0;

        while ($cursor < $length) {
            $pos = strpos($string, '|', $cursor);

            if ($pos === false) {
                // we're in the last rule
                $pos = $length;
            }

            $rule = substr($string, $cursor, $pos - $cursor);

            $rules[] = $rule;
            $cursor += strlen($rule) + 1; // +1 to exclude the pipe
        }

        return array_unique($rules);
    }

    /**
     * Valid if field is valid email.
     *
     * @return ValidationRule
     */
    public static function email(): ValidationRule {
        return new Email();
    }

    /**
     * Validate field required.
     *
     * @return ValidationRule
     */
    public static function required(): ValidationRule {
        return new Required();
    }

    /**
     * Validate field to check when validating actual field
     *
     * @param string $withField
     * @return ValidationRule
     */
    public static function requiredWith(string $withField): ValidationRule {
        return new RequiredWith($withField);
    }

    /**
     * Validates the field when the other field meets the rule.
     *
     * @param string $otherField
     * @param string $operator
     * @param string $compareWith
     * @return ValidationRule
     */
    public static function requiredWhen(string $otherField, string $operator, string $compareWith): ValidationRule {
        return new RequiredWhen($otherField, $operator, $compareWith);
    }

    /**
     * Valid if the field is a valid number.
     *
     * @return ValidationRule
     */
    public static function number(): ValidationRule {
        return new Number();
    }

    /**
     * Valid that the field is less than
     *
     * @param float $lessThan
     * @return ValidationRule
     */
    public static function lessThan(float $lessThan): ValidationRule {
        return new LessThan($lessThan);
    }
}
