<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

interface IdGenerator
{
    public function mediaId(): MediaId;
}
