<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Metadata;

use Damax\Media\Domain\Model\Metadata;

class Collector implements Reader
{
    /**
     * @var Reader[]
     */
    private $items = [];

    public function __construct(iterable $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(Reader $reader): void
    {
        $this->items[] = $reader;
    }

    public function supports($context): bool
    {
        foreach ($this->items as $item) {
            if ($item->supports($context)) {
                return true;
            }
        }

        return false;
    }

    public function extract($context): Metadata
    {
        $reduce = function (Metadata $metadata, Reader $reader) use ($context): Metadata {
            return $metadata->merge($reader->extract($context));
        };

        $filter = function (Reader $reader) use ($context): bool {
            return $reader->supports($context);
        };

        return array_reduce(array_filter($this->items, $filter), $reduce, Metadata::blank());
    }
}
