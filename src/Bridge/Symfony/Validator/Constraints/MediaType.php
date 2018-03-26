<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class MediaType extends Constraint
{
    /**
     * @var string
     */
    public $message = 'damax_media.type.invalid';
}
