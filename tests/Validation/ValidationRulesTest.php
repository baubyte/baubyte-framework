<?php

namespace Baubyte\Tests\Validation;

use Baubyte\Validation\Exceptions\RuleParseException;
use Baubyte\Validation\Exceptions\UnknownRuleException;
use Baubyte\Validation\Rule;
use Baubyte\Validation\Rules\Email;
use Baubyte\Validation\Rules\LessThan;
use Baubyte\Validation\Rules\Nullable;
use Baubyte\Validation\Rules\Number;
use Baubyte\Validation\Rules\Required;
use Baubyte\Validation\Rules\RequiredWhen;
use Baubyte\Validation\Rules\RequiredWith;
use PHPUnit\Framework\TestCase;

class ValidationRulesTest extends TestCase {
    public function emails() {
        return [
            ["test@test.com", true],
            ["admin@baubyte.com", true],
            ["test@testcom", false],
            ["test@test.", false],
            ["baubyte@", false],
            ["baubyte@.", false],
            ["baubyte", false],
            ["@", false],
            ["", false],
            [null, false],
            [4, false],
        ];
    }

    /**
     * @dataProvider emails
     */
    public function test_email($email, $expected) {
        $data = ["email" => $email];
        $rule = new Email();
        $this->assertEquals($expected, $rule->isValid('email', $data));
    }

    public function requiredData() {
        return [
            ["", false],
            [null, false],
            [5, true],
            ["test", true],
        ];
    }

    /**
     * @dataProvider requiredData
     */
    public function test_required($value, $expected) {
        $data = ["test" => $value];
        $rule = new Required();
        $this->assertEquals($expected, $rule->isValid('test', $data));
    }

    public function test_required_with() {
        $rule = new RequiredWith("other");
        $data = ["other" => 10, "test" => 5];
        $this->assertTrue($rule->isValid('test', $data));
        $data = ["other" => 10];
        $this->assertFalse($rule->isValid('test', $data));
    }

    public function whenData() {
        return [
            ["other", "=", "value", ["other" => "value"], "test", false],
            ["other", "=", "value", ["other" => "value", "test" => 1], "test", true],
            ["other", "=", "value", ["other" => "not value"], "test", true],
            ["other", ">", 5, ["other" => 1], "test", true],
            ["other", ">", 5, ["other" => 6], "test", false],
            ["other", ">", 5, ["other" => 6, "test" => 1], "test", true],
        ];
    }

    /**
     * @dataProvider whenData
     */
    public function test_required_when($other, $operator, $compareWith, $data, $field, $expected) {
        $rule = new RequiredWhen($other, $operator, $compareWith);
        $this->assertEquals($expected, $rule->isValid($field, $data));
    }

    public function numbers() {
        return [
            [0, true],
            [1, true],
            [1.5, true],
            [-1, true],
            [-1.5, true],
            ["0", true],
            ["1", true],
            ["1.5", true],
            ["-1", true],
            ["-1.5", true],
            ["test", false],
            ["1test", false],
            ["-5test", false],
            ["", false],
            [null, false],
        ];
    }
    /**
     * @dataProvider numbers
     */
    public function test_number($number, $expected) {
        $rule = new Number();
        $data = ["test" => $number];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }


    public function dataLessThan() {
        return [
            [5, 5, false],
            [5, 6, false],
            [5, 3, true],
            [5, null, false],
            [5, "", false],
            [5, "test", false],
        ];
    }

    /**
     * @dataProvider dataLessThan
     */
    public function testLessThan($value, $check, $expected) {
        $rule = new LessThan($value);
        $data = ["test" => $check];
        $this->assertEquals($expected, $rule->isValid("test", $data));
    }

    public function testNullable() {
        $rule = new Nullable();
        foreach (["test", "", null] as $check) {
            $data = ["test" => $check];
            $this->assertTrue($rule->isValid("test", $data));
        }
    }

    public function test_required_when_throws_parse_rule_exception_when_operator_is_invalid() {
        $rule = new RequiredWhen("other", "|||", "test");
        $data = ["other" => 5, "test" => 1];
        $this->expectException(RuleParseException::class);
        $rule->isValid("test", $data);
    }
}
