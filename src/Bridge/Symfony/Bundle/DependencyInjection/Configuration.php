<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('damax_media');
        $rootNode
            ->children()
                ->append($this->typeNode('types'))
                ->integerNode('key_length')
                    ->defaultValue(8)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function typeNode(string $name): ArrayNodeDefinition
    {
        $toMegabytes = function (int $size): int {
            return $size * 1024 * 1024;
        };

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
                            ->then($toMegabytes)
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
}
