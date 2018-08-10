<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Domain\Model\FileInfo;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaId;

final class UploadedImageMedia extends Media
{
    public function __construct()
    {
        $id = MediaId::fromString('64c2c4b7-33f5-11e8-97f3-005056806fb2');

        $info = new FileInfo('image/png', 1024);

        $file = (new FileFactory())->createPng();

        parent::__construct($id, 'image', 'Test PNG image', $info);

        $this->upload($file, Metadata::create());
    }
}
