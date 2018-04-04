<?php

declare(strict_types=1);

namespace Damax\Media\Metadata;

use Damax\Media\Domain\Metadata\Reader;
use Damax\Media\Domain\Model\Metadata;

class GdImageReader implements Reader
{
    public function supports($context): bool
    {
        return 0 === strpos($context['mime_type'] ?? 'plain/text', 'image/');
    }

    public function extract($context): Metadata
    {
        $image = imagecreatefromstring($context['stream']);

        $data = [
            'width' => imagesx($image),
            'height' => imagesy($image),
        ];

        imagedestroy($image);

        return Metadata::fromArray($data);
    }
}
