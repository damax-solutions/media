<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Validator\Constraints;

use Damax\Media\Type\Types;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MediaTypeValidator extends ConstraintValidator
{
    private $types;

    public function __construct(Types $types)
    {
        $this->types = $types;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MediaType) {
            throw new UnexpectedTypeException($constraint, MediaType::class);
        }

        if (!$value) {
            return;
        }

        if ($this->types->hasDefinition($value)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->setInvalidValue($value)
            ->addViolation()
        ;
    }
}
