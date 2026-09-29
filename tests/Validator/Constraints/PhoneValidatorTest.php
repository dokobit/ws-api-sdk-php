<?php

namespace Dokobit\Tests\Validator\Constraints;

use Dokobit\Validator\Constraints\Phone;
use Dokobit\Validator\Constraints\PhoneValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PhoneValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new PhoneValidator(false);
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Phone());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new Phone());

        $this->assertNoViolation();
    }

    public function testExpectsStringCompatibleType()
    {
        $this->expectException(\Symfony\Component\Validator\Exception\UnexpectedTypeException::class);
        $this->validator->validate(new \stdClass(), new Phone());
    }

    /**
     * @dataProvider getValidPhones
     */
    public function testValidPhones($phone)
    {
        $this->validator->validate($phone, new Phone());

        $this->assertNoViolation();
    }

    public function getValidPhones()
    {
        return array(
            // Lithuania
            array('+37060000000'),
            // Estonia
            array('+3720000000'),
            array('+37200000000'),
            // // Finland
            array('+358409999'),
            array('+3584099999'),
            array('+35840999999'),
            array('+358409999999'),
            array('+3584099999999'),
            array('+3584579999'),
            array('+358509999'),
        );
    }

    /**
     * @dataProvider getInvalidPhones
     */
    public function testInvalidPhones($phone)
    {
        $constraint = new Phone(array(
            'message' => 'myMessage',
        ));

        $this->validator->validate($phone, $constraint);

        $this->buildViolation('myMessage')
            ->setParameter('{{ value }}', '"'.$phone.'"')
            ->setParameter('{{ pattern }}', $constraint->pattern)
            ->setCode(defined('Dokobit\Validator\Constraints\Phone::REGEX_FAILED_ERROR')?Phone::REGEX_FAILED_ERROR:null)
            ->assertRaised();
    }

    public function getInvalidPhones()
    {
        return array(
            array('1'), // too short
            array('personal_cde'), // random crap
            array('+00000000000'), // invalid
            // Lithuania
            array('+37000000000'), // invalid
            array('+3706000000000'), // invalid
            // Estonia
            array('+372000000'),
            array('+372000000000'),
            // // Finland
            array('+35840999'),
            array('+35840999999999'),
        );
    }
}
