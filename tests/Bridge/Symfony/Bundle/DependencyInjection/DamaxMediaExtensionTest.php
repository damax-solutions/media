<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Bridge\Symfony\Bundle\DependencyInjection\DamaxMediaExtension;
use Damax\Media\Domain\Model\ConfigurableMediaFactory;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Type\Types;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Definition;

class DamaxMediaExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @test
     */
    public function it_registers_services()
    {
        $this->load([
            'key_length' => 12,
            'types' => [
                'document' => [
                    'storage' => 's3',
                    'max_file_size' => 4,
                    'mime_types' => ['application/pdf'],
                ],
                'image' => [
                    'storage' => 'local',
                    'max_file_size' => 8,
                    'mime_types' => ['image/jpg', 'image/png', 'image/gif'],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter('damax.media.media_class');
        $this->assertContainerBuilderHasParameter('damax.media.key_length', 12);

        $this->assertContainerBuilderHasService(Assembler::class);
        $this->assertContainerBuilderHasService(Types::class);
        $this->assertContainerBuilderHasService(MediaFactory::class, ConfigurableMediaFactory::class);

        $types = $this->container->getDefinition(Types::class)->getArgument(0);
        $this->assertCount(2, $types);
        $this->assertTypeDefinition('s3', 4194304, ['application/pdf'], $types['document']);
        $this->assertTypeDefinition('local', 8388608, ['image/jpg', 'image/png', 'image/gif'], $types['image']);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new DamaxMediaExtension(),
        ];
    }

    private function assertTypeDefinition(string $storage, int $maxFileSize, array $mimeTypes, Definition $definition): void
    {
        $this->assertEquals($storage, $definition->getArgument(0));
        $this->assertEquals($maxFileSize, $definition->getArgument(1));
        $this->assertEquals($mimeTypes, $definition->getArgument(2));
    }
}
