<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Bridge\Symfony\Bundle\DependencyInjection\DamaxMediaExtension;
use Damax\Media\Domain\Model\ConfigurableMediaFactory;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Type\Types;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class DamaxMediaExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_registers_services()
    {
        $this->load([]);

        $this->assertContainerBuilderHasParameter('damax.media.media_class');

        $this->assertContainerBuilderHasService(Assembler::class);
        $this->assertContainerBuilderHasService(Types::class);
        $this->assertContainerBuilderHasService(MediaFactory::class, ConfigurableMediaFactory::class);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new DamaxMediaExtension(),
        ];
    }
}
