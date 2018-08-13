<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Bridge\Symfony\Validator\Constraints;

use Damax\Media\Bridge\Symfony\Validator\Constraints\MediaType;
use Damax\Media\Bridge\Symfony\Validator\Constraints\MediaTypeValidator;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class MediaTypeValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @var Types
     */
    private $types;

    protected function setUp()
    {
        $this->types = new Types();

        parent::setUp();
    }

    /**
     * @test
     */
    public function it_fails_to_validate_on_unsupported_constraint()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage(sprintf('Expected argument of type "%s", "%s" given', MediaType::class, NotBlank::class));

        $this->validator->validate('__test__', new NotBlank());
    }

    /**
     * @test
     */
    public function it_has_no_violations_when_value_is_empty()
    {
        $this->validator->validate('', new MediaType());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function it_has_no_violations()
    {
        $this->types->addDefinition('document', new Definition('s3', 1024, ['application/pdf']));

        $this->validator->validate('document', new MediaType());

        $this->assertNoViolation();
    }

    /**
     * @test
     */
    public function it_raises_violation_on_unregistered_media_type()
    {
        $this->validator->validate('document', new MediaType());

        $this
            ->buildViolation('damax_media.type.invalid')
            ->setInvalidValue('document')
            ->assertRaised()
        ;
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new MediaTypeValidator($this->types);
    }
}
