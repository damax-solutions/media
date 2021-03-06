<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaId;

final class PendingPdfMedia extends Media
{
    public function __construct()
    {
        $id = MediaId::fromString('183702c5-30de-11e8-97f3-005056806fb2');

        $file = (new FileFactory())->createPdf();

        parent::__construct($id, 'document', 'Test PDF document', $file->info());
    }
}
