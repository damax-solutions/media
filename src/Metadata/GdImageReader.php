<?php

declare(strict_types=1);

namespace Damax\Media\Metadata;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Domain\Metadata\Reader;

final class GdImageReader implements Reader
{
    public function supports($context): bool
    {
        if (!extension_loaded('gd')) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
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
