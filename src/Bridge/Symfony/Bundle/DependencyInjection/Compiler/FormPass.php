<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FormPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $resources = $container->getParameter('twig.form.resources');

        $container->setParameter('twig.form.resources', array_merge($resources, [
            '@DamaxMedia/Form/form_layout.html.twig',
            '@DamaxMedia/Form/form_javascript.html.twig',
        ]));
    }
}
