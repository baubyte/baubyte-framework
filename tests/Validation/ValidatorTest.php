<?php

namespace Baubyte\Tests\Validation;

use Baubyte\Validation\Exceptions\ValidationException;
use Baubyte\Validation\Rule;
use Baubyte\Validation\Rules\Email;
use Baubyte\Validation\Rules\LessThan;
use Baubyte\Validation\Rules\Number;
use Baubyte\Validation\Rules\Required;
use Baubyte\Validation\Rules\RequiredWith;
use Baubyte\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {
    protected function setUp(): void {
        Rule::loadDefaultRules();
    }

    public function test_basic_validation_passes() {
        $data = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
            "foo" => 5,
            "bar" => 4
        ];

        $rules = [
            "email" => new Email(),
            "other" => new Required(),
            "num" => new Number(),
        ];

        $expected = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
        ];

        $validator = new Validator($data);

        $this->assertEquals($expected, $validator->validate($rules));
    }

    public function test_throws_validation_exception_on_invalid_data() {
        $this->expectException(ValidationException::class);
        $validator = new Validator(["test" => "test"]);
        $validator->validate(["test" => new Number()]);
    }

    /**
     * @depends test_basic_validation_passes
     */
    public function test_multiple_rules_validation() {
        $data = ["age" => 20, "num" => 3, "foo" => 5];

        $rules = [
            "age" => new LessThan(100),
            "num" => [new RequiredWith("age"), new Number()],
        ];

        $expected = ["age" => 20, "num" => 3];

        $v = new Validator($data);

        $this->assertEquals($expected, $v->validate($rules));
    }

    public function test_overrides_error_messages_correctly() {
        $data = ["email" => "test@", "num1" => "not a number"];

        $rules = [
            "email" => "email",
            "num1" => "number",
            "num2" =>  ["required", "number"],
        ];

        $messages = [
            "email" => ["email" => "test email message"],
            "num1" => ["number" => "test number message"],
            "num2" =>  [
                "required" => "test required message",
                "number" => "test number message again"
            ]
        ];

        $validator = new Validator($data);

        try {
            $validator->validate($rules, $messages);
            $this->fail("Did not throw ValidationException");
        } catch (ValidationException $e) {
            $this->assertEquals($messages, $e->errors());
        }
    }

    public function test_basic_validation_passes_with_strings() {
        $data = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
            "foo" => 5,
            "bar" => 4
        ];

        $rules = [
            "email" => "email",
            "other" => "required|number",
            "num" => "number",
        ];

        $expected = [
            "email" => "test@test.com",
            "other" => 2,
            "num" => 3,
        ];

        $validator = new Validator($data);

        $this->assertEquals($expected, $validator->validate($rules));
    }


    public function test_returns_messages_for_each_rule_that_doesnt_pass() {
        $email = new Email();
        $required = new Required();
        $number = new Number();

        $data = ["email" => "test@", "num1" => "not a number"];

        $rules = [
            "email" => $email,
            "num1" => $number,
            "num2" => [$required, $number],
        ];
        $expected = [
            "email" => ["email" => $email->message()],
            "num1" => ["number" => $number->message()],
            "num2" => [
                "required" => $required->message(),
                "number" => $number->message()
            ],
        ];

        $validator = new Validator($data);

        try {
            $validator->validate($rules);
            $this->fail("Did not throw Validation Exception");
        } catch (ValidationException $e) {
            $this->assertEquals($expected, $e->errors());
        }
    }
}
