<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Application\Dto\UploadDto;
use Damax\Media\Domain\Model\MediaId;

final class UploadMedia
{
    private $mediaId;
    private $upload;

    public function __construct(string $mediaId, UploadDto $upload)
    {
        $this->mediaId = $mediaId;
        $this->upload = $upload;
    }

    public function mediaId(): MediaId
    {
        return MediaId::fromString($this->mediaId);
    }

    public function upload(): UploadDto
    {
        return $this->upload;
    }
}
