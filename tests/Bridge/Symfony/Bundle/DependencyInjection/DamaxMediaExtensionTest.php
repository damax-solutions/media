<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Bridge\Symfony\Bundle\DependencyInjection\DamaxMediaExtension;
use Damax\Media\Domain\Image\UrlBuilder;
use Damax\Media\Domain\Model\ConfigurableMediaFactory;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\RandomKeys;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Flysystem\FlysystemStorage;
use Damax\Media\Gaufrette\GaufretteStorage;
use Damax\Media\Glide\SignedUrlBuilder;
use Damax\Media\Type\Types;
use Gaufrette\FilesystemMap;
use Gaufrette\StreamWrapper;
use League\Glide\Signatures\SignatureInterface;
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
            'storage' => [
                'adapter' => 'gaufrette',
            ],
        ]);

        $this->assertContainerBuilderHasParameter('damax.media.media_class');

        $this->assertContainerBuilderHasService(Assembler::class);
        $this->assertContainerBuilderHasService(Types::class);
        $this->assertContainerBuilderHasService(MediaFactory::class, ConfigurableMediaFactory::class);

        // Assert types.
        $types = $this->container->getDefinition(Types::class)->getArgument(0);
        $this->assertCount(2, $types);
        $this->assertTypeDefinition('s3', 4194304, ['application/pdf'], $types['document']);
        $this->assertTypeDefinition('local', 8388608, ['image/jpg', 'image/png', 'image/gif'], $types['image']);
    }

    /**
     * @test
     */
    public function it_registers_gaufrette_storage()
    {
        $this->load([
            'storage' => [
                'adapter' => 'gaufrette',
                'key_length' => 12,
            ],
        ]);

        $this->assertContainerBuilderHasService(Storage::class, GaufretteStorage::class);
        $this->assertContainerBuilderHasService(Keys::class, RandomKeys::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Keys::class, 1, 12);
        $this->assertContainerBuilderHasService(FilesystemMap::class);

        // Assert factory.
        $definition = $this->container->getDefinition(FilesystemMap::class);
        $this->assertEquals([StreamWrapper::class, 'getFilesystemMap'], $definition->getFactory());
    }

    /**
     * @test
     */
    public function it_registers_flysystem_storage()
    {
        $this->load([
            'storage' => [
                'adapter' => 'flysystem',
                'key_length' => 16,
                'sign_key' => 'Qwerty12',
            ],
            'glide' => [
                'driver' => 'gd',
                'source' => 'foo',
                'cache' => 'bar',
            ],
        ]);

        $this->assertContainerBuilderHasService(Storage::class, FlysystemStorage::class);
        $this->assertContainerBuilderHasService(Keys::class, RandomKeys::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Keys::class, 1, 16);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(SignatureInterface::class, 0, 'Qwerty12');
        $this->assertContainerBuilderHasService(UrlBuilder::class, SignedUrlBuilder::class);

        $this->assertContainerBuilderHasParameter('damax.media.glide.server', [
            'driver' => 'gd',
            'source' => 'foo',
            'cache' => 'bar',
            'group_cache_in_folders' => true,
            'presets' => [],
        ]);
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
