<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\Media\Domain\Model\Media;
use Damax\Media\Type\Definition as TypeDefinition;
use Damax\Media\Type\Types;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DamaxMediaExtension extends ConfigurableExtension
{
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('doctrine-orm.xml');

        $this->configureTypes($config['types'], $container);

        $container->setParameter('damax.media.media_class', Media::class);
        $container->setParameter('damax.media.key_length', $config['key_length']);
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

        $container->getDefinition(Types::class)->setArgument(0, $types);

        return $this;
    }
}
