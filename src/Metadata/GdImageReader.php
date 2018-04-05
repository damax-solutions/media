<?php

declare(strict_types=1);

namespace Damax\Media\Metadata;

use Damax\Media\Domain\Metadata\Reader;
use Damax\Media\Domain\Model\Metadata;

class GdImageReader implements Reader
{
    public function supports($context): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        return 0 === strpos($context['mime_type'] ?? 'plain/text', 'image/');
    }

    public function extract($context): Metadata
    {
        rewind($context['stream']);

        $image = imagecreatefromstring(stream_get_contents($context['stream']));

        $data = [
            'width' => imagesx($image),
            'height' => imagesy($image),
        ];

        imagedestroy($image);

        return Metadata::fromArray($data);
    }
}
