<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Bridge\Symfony\Validator\Constraints;

use Damax\Media\Bridge\Symfony\Validator\Constraints\Format;
use Damax\Media\Bridge\Symfony\Validator\Constraints\FormatValidator;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @covers \Damax\Media\Bridge\Symfony\Validator\Constraints\Format
 * @covers \Damax\Media\Bridge\Symfony\Validator\Constraints\FormatValidator
 */
class FormatValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var Types
     */
    private $types;

    protected function setUp()
    {
        $definition = new Definition('s3', 1024, ['application/pdf', 'application/json']);

        $this->types = new Types();
        $this->types->addDefinition('document', $definition);

        parent::setUp();
    }

    /**
     * @test
     */
    public function it_fails_to_validate_on_unsupported_constraint()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(sprintf('Expected argument of type "%s", "%s" given', Format::class, NotBlank::class));

        $this->validator->validate('__test__', new NotBlank());
    }

    /**
     * @test
     */
    public function it_has_no_violations_when_value_is_empty()
    {
        $this->validator->validate('', new Format());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function it_has_no_violations_when_type_is_empty()
    {
        $this->validator->validate((object) ['type' => ''], new Format());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function it_has_no_violations_when_type_is_missing()
    {
        $this->validator->validate((object) ['type' => 'image'], new Format());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function it_has_no_violations()
    {
        $this->validator->validate((object) ['type' => 'document', 'mimeType' => 'application/pdf'], new Format());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function it_raises_violation_on_invalid_mime_type()
    {
        $this->validator->validate((object) ['type' => 'document', 'mimeType' => 'application/xml'], new Format(['propertyPath' => 'fieldName']));

        $this
            ->buildViolation('damax_media.format.invalid')
            ->atPath('property.path.fieldName')
            ->setInvalidValue('application/xml')
            ->assertRaised()
        ;
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new FormatValidator($this->types);
    }
}
