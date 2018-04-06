<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Listener;

use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Domain\Model\MediaRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\AcceptHeaderItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig_Environment;

class MediaUploadFormListener implements EventSubscriberInterface
{
    private $repository;
    private $twig;

    public function __construct(MediaRepository $repository, Twig_Environment $twig)
    {
        $this->repository = $repository;
        $this->twig = $twig;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 8],
        ];
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if ('media_upload' !== $request->attributes->get('_route') || $event->getResponse()) {
            return;
        }

        if (!count(array_filter($request->getAcceptableContentTypes(), [$this, 'acceptHtml']))) {
            return;
        }

        $dto = $event->getControllerResult();

        if (!$dto instanceof MediaDto) {
            return;
        }

        $params = json_decode($request->query->get('params', '{}'), true);

        $media = $this->repository->byId(Uuid::fromString($dto->id));

        $template = $this->twig
            ->loadTemplate('DamaxMediaBundle:Form:form_layout.html.twig')
            ->renderBlock('damax_media_item', array_merge($params, ['media' => $media]))
        ;

        $event->setResponse(Response::create($template, Response::HTTP_CREATED));
    }

    private function acceptHtml(string $accept): bool
    {
        return 'text/html' === $accept;
    }
}
