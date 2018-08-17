<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class Format extends Constraint
{
    /**
     * @var string
     */
    public $message = 'damax_media.format.invalid';

    /**
     * @var string
     */
    public $propertyPath = 'mimeType';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
