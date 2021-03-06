<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\Media\Application\Query\GetImageHandler;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Image\UrlBuilder;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\Keys\Keys;
use Damax\Media\Domain\Storage\Keys\RandomKeys;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Glide\GlideManipulator;
use Damax\Media\Glide\SignedUrlBuilder;
use Damax\Media\Type\Definition as TypeDefinition;
use Damax\Media\Type\Types;
use Gaufrette\FilesystemMap;
use Gaufrette\StreamWrapper;
use League\Glide\Signatures\Signature;
use League\Glide\Signatures\SignatureInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class DamaxMediaExtension extends ConfigurableExtension
{
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('doctrine-orm.xml');

        $this
            ->configureStorage($config['storage'], $container)
            ->configureTypes($config['types'], $container)
        ;

        $container->setParameter('damax.media.media_class', Media::class);

        if (!empty($config['glide'])) {
            $this->configureGlide($config['glide'], $container);
        } else {
            $container->removeDefinition(GetImageHandler::class);
        }
    }

    private function configureStorage(array $config, ContainerBuilder $container): self
    {
        $storage = ucfirst($config['adapter']);

        $container->autowire(Storage::class, sprintf('Damax\\Media\\%s\\%sStorage', $storage, $storage));

        $container
            ->autowire(Keys::class, RandomKeys::class)
            ->setArgument(1, $config['key_length'])
        ;

        if (Configuration::ADAPTER_GAUFRETTE === $config['adapter']) {
            $container
                ->register(FilesystemMap::class)
                ->setFactory(StreamWrapper::class . '::getFilesystemMap')
            ;
        }

        return $this;
    }

    private function configureTypes(array $config, ContainerBuilder $container): self
    {
        $types = [];

        foreach ($config as $name => $item) {
            $types[$name] = new Definition(TypeDefinition::class, [
                $item['storage'],
                $item['max_file_size'],
                $item['mime_types'],
            ]);
        }

        $container
            ->register(Types::class)
            ->addArgument($types)
        ;

        return $this;
    }

    private function configureGlide(array $config, ContainerBuilder $container): self
    {
        $serverParams = $config;
        unset($serverParams['sign_key']);

        $container
            ->autowire(SignatureInterface::class, Signature::class)
            ->addArgument($config['sign_key'])
        ;
        $container
            ->autowire(Manipulator::class, GlideManipulator::class)
            ->setArgument(3, $serverParams)
        ;

        $container->autowire(UrlBuilder::class, SignedUrlBuilder::class);

        return $this;
    }
}
