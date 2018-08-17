<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Validator\Constraints;

use Damax\Media\Type\Types;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class FormatValidator extends ConstraintValidator
{
    private $types;

    public function __construct(Types $types)
    {
        $this->types = $types;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Format) {
            throw new UnexpectedTypeException($constraint, Format::class);
        }

        if (!$value->type || !$this->types->hasDefinition($value->type)) {
            return;
        }

        $mimeTypes = $this->types->definition($value->type)->mimeTypes();

        if (!in_array($value->mimeType, $mimeTypes)) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->propertyPath)
                ->setInvalidValue($value->mimeType)
                ->addViolation()
            ;
        }
    }
}
