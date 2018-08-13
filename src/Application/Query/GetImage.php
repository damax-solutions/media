<?php

declare(strict_types=1);

namespace Application\Query;

final class GetImage extends MediaQuery
{
    private $params;

    public function __construct(string $mediaId, array $params)
    {
        parent::__construct($mediaId);

        $this->params = $params;
    }

    public function params(): array
    {
        return $this->params;
    }
}
