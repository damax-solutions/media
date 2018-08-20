<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\Common\Bridge\Symfony\Serializer\EntityIdNormalizer;
use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Bridge\Symfony\Bundle\DependencyInjection\DamaxMediaExtension;
use Damax\Media\Domain\FileFormatter;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Image\UrlBuilder;
use Damax\Media\Domain\Metadata\Collector;
use Damax\Media\Domain\Metadata\Reader;
use Damax\Media\Domain\Model\DefaultMediaFactory;
use Damax\Media\Domain\Model\IdGenerator;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Domain\Model\MediaId;
use Damax\Media\Domain\Model\UuidIdGenerator;
use Damax\Media\Domain\Storage\Guesser\Guesser;
use Damax\Media\Domain\Storage\Guesser\SymfonyGuesser;
use Damax\Media\Domain\Storage\Keys\Keys;
use Damax\Media\Domain\Storage\Keys\RandomKeys;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Flysystem\FlysystemStorage;
use Damax\Media\Gaufrette\GaufretteStorage;
use Damax\Media\Glide\SignedUrlBuilder;
use Damax\Media\Metadata\GdImageReader;
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
        $this->load(['storage' => 'flysystem']);

        $this->assertContainerBuilderHasParameter('damax.media.media_class');
        $this->assertContainerBuilderHasService(Assembler::class);
        $this->assertContainerBuilderHasService(MediaFactory::class, DefaultMediaFactory::class);
        $this->assertContainerBuilderHasService(IdGenerator::class, UuidIdGenerator::class);
        $this->assertContainerBuilderHasService(Guesser::class, SymfonyGuesser::class);
        $this->assertContainerBuilderHasService(Reader::class, Collector::class);
        $this->assertContainerBuilderHasService(FileFormatter::class);
        $this->assertContainerBuilderHasService(GdImageReader::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag(GdImageReader::class, 'damax.media.reader');

        $this->assertContainerBuilderHasService('damax.media.normalizer.media_id', EntityIdNormalizer::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('damax.media.normalizer.media_id', 0, MediaId::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag('damax.media.normalizer.media_id', 'serializer.normalizer');
    }

    /**
     * @test
     */
    public function it_registers_gaufrette_services()
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

        $factory = $this->container
            ->getDefinition(FilesystemMap::class)
            ->getFactory()
        ;
        $this->assertEquals([StreamWrapper::class, 'getFilesystemMap'], $factory);
    }

    /**
     * @test
     */
    public function it_registers_flysystem_services()
    {
        $this->load(['storage' => 'flysystem']);

        $this->assertContainerBuilderHasService(Storage::class, FlysystemStorage::class);
        $this->assertContainerBuilderHasService(Keys::class, RandomKeys::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Keys::class, 1, 8);
    }

    /**
     * @test
     */
    public function it_configures_types()
    {
        $this->load([
            'storage' => 'flysystem',
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

        // Assert types.
        $types = $this->container
            ->getDefinition(Types::class)
            ->getArgument(0)
        ;

        $this->assertCount(2, $types);
        $this->assertTypeDefinition('s3', 4194304, ['application/pdf'], $types['document']);
        $this->assertTypeDefinition('local', 8388608, ['image/jpg', 'image/png', 'image/gif'], $types['image']);
    }

    /**
     * @test
     */
    public function it_registers_glide_services()
    {
        $this->load([
            'storage' => 'flysystem',
            'glide' => [
                'driver' => 'gd',
                'source' => 's3',
                'cache' => 'local',
                'sign_key' => 'secret',
            ],
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(SignatureInterface::class, 0, 'secret');
        $this->assertContainerBuilderHasService(UrlBuilder::class, SignedUrlBuilder::class);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(Manipulator::class, 3, [
            'driver' => 'gd',
            'source' => 's3',
            'cache' => 'local',
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
