<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\FileInfo;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaId;

final class PendingPdfMedia extends Media
{
    public function __construct()
    {
        $id = MediaId::fromString('183702c5-30de-11e8-97f3-005056806fb2');

        $info = new FileInfo('application/pdf', 1024);

        parent::__construct($id, 'document', 'Test PDF document', $info);
    }
}
