<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Application\Dto\UploadDto;

final class UploadMedia extends MediaCommand
{
    private $upload;

    public function __construct(string $mediaId, UploadDto $upload)
    {
        parent::__construct($mediaId);

        $this->upload = $upload;
    }

    public function upload(): UploadDto
    {
        return $this->upload;
    }
}
