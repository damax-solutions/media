<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle;

use Damax\Media\Bridge\Symfony\Bundle\DependencyInjection\Compiler\FormPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DamaxMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FormPass());
    }
}
