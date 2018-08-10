<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Metadata;

use Damax\Common\Domain\Model\Metadata;

interface Reader
{
    public function supports($context): bool;

    public function extract($context): Metadata;
}
