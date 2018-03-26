<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaInfo;
use Ramsey\Uuid\Uuid;

class PendingPdfMedia extends Media
{
    public function __construct()
    {
        $id = Uuid::fromString('183702c5-30de-11e8-97f3-005056806fb2');

        $info = new MediaInfo('application/pdf', 1024);

        parent::__construct($id, 'document', 'Test PDF document', $info);
    }
}
