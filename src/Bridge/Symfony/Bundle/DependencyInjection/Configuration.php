<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\DependencyInjection;

use Closure;
use Damax\Media\Domain\Image\Manipulator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const ADAPTER_GAUFRETTE = 'gaufrette';
    public const ADAPTER_FLYSYSTEM = 'flysystem';

    public const DRIVER_GD = 'gd';
    public const DRIVER_IMAGICK = 'imagick';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('damax_media');

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->append($this->storageNode('storage'))
                ->append($this->typesNode('types'))
                ->append($this->glideNode('glide'))
            ->end()
        ;

        return $treeBuilder;
    }

    private function storageNode(string $name): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition($name))
            ->isRequired()
            ->beforeNormalization()
                ->ifString()
                ->then(function (string $adapter) {
                    return ['adapter' => $adapter];
                })
            ->end()
            ->children()
                ->enumNode('adapter')
                    ->isRequired()
                    ->values([self::ADAPTER_GAUFRETTE, self::ADAPTER_FLYSYSTEM])
                ->end()
                ->integerNode('key_length')
                    ->defaultValue(8)
                ->end()
            ->end()
        ;
    }

    private function typesNode(string $name): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition($name))
            ->prototype('array')
                ->children()
                    ->scalarNode('storage')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->integerNode('max_file_size')
                        ->beforeNormalization()
                            ->always()
                            ->then(Closure::fromCallable([$this, 'toMegabytes']))
                        ->end()
                        ->isRequired()
                    ->end()
                    ->arrayNode('mime_types')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function glideNode(string $name): ArrayNodeDefinition
    {
        return (new ArrayNodeDefinition($name))
            ->children()
                ->enumNode('driver')
                    ->isRequired()
                    ->values([self::DRIVER_GD, self::DRIVER_IMAGICK])
                ->end()
                ->scalarNode('source')
                    ->isRequired()
                ->end()
                ->scalarNode('cache')
                    ->isRequired()
                ->end()
                ->booleanNode('group_cache_in_folders')
                    ->defaultTrue()
                ->end()
                ->integerNode('max_image_size')
                    ->beforeNormalization()
                        ->always()
                        ->then(Closure::fromCallable([$this, 'toMegabytes']))
                    ->end()
                ->end()
                ->scalarNode('sign_key')
                    ->cannotBeEmpty()
                    ->defaultValue('%kernel.secret%')
                ->end()
                ->arrayNode('presets')
                    ->useAttributeAsKey(true)
                    ->variablePrototype()
                        ->validate()
                            ->ifTrue(function (array $config): bool {
                                return !Manipulator::validParams($config);
                            })
                            ->thenInvalid('Invalid manipulation specified.')
                        ->end()
                    ->end()
                ->end()
                ->variableNode('defaults')
                    ->validate()
                        ->ifTrue(function (array $config): bool {
                            return !Manipulator::validParams($config);
                        })
                        ->thenInvalid('Invalid manipulation specified.')
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function toMegabytes(int $size): int
    {
        return $size * 1024 * 1024;
    }
}
