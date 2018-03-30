<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaInfo;
use Ramsey\Uuid\Uuid;

class PendingImageMedia extends Media
{
    public function __construct()
    {
        $id = Uuid::fromString('64c2c4b7-33f5-11e8-97f3-005056806fb2');

        $info = new MediaInfo('image/png', 1024);

        parent::__construct($id, 'image', 'Test PNG image', $info);
    }
}
