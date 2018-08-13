<?php

declare(strict_types=1);

namespace Bridge\Symfony\Bundle\Controller\Api;

use Damax\Common\Bridge\Symfony\Bundle\Annotation\Serialize;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Query\FetchMedia;
use League\Tactician\CommandBus as QueryBus;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as OpenApi;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/media")
 */
final class MediaQueryController
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * @OpenApi\Get(
     *     tags={"media"},
     *     summary="Get media.",
     *     security={
     *         {"Bearer"=""}
     *     },
     *     @OpenApi\Response(
     *         response=200,
     *         description="Media info.",
     *         @OpenApi\Schema(ref=@Model(type=MediaDto::class))
     *     ),
     *     @OpenApi\Response(
     *         response=404,
     *         description="Media not found."
     *     )
     * )
     *
     * @Route("/{id}", methods={"GET})
     * @Serialize()
     *
     * @throws NotFoundHttpException
     */
    public function getAction(string $id): MediaDto
    {
        try {
            return $this->queryBus->handle(new FetchMedia($id));
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        }
    }
}
