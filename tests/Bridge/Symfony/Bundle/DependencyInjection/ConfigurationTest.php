<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Bridge\Symfony\Bundle\DependencyInjection;

use Damax\Media\Bridge\Symfony\Bundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @test
     */
    public function it_processes_empty_config()
    {
        $config = ['storage' => 'flysystem'];

        $this->assertProcessedConfigurationEquals([$config], [
            'types' => [],
            'storage' => [
                'adapter' => 'flysystem',
                'key_length' => 8,
            ],
        ]);
    }

    /**
     * @test
     */
    public function it_processes_simplified_storage_config()
    {
        $config = ['storage' => 'gaufrette'];

        $this->assertProcessedConfigurationEquals([$config], [
            'storage' => [
                'adapter' => 'gaufrette',
                'key_length' => 8,
            ],
        ], 'storage');
    }

    /**
     * @test
     */
    public function it_processes_storage_config()
    {
        $config = [
            'storage' => [
                'adapter' => 'gaufrette',
                'key_length' => 12,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'storage' => [
                'adapter' => 'gaufrette',
                'key_length' => 12,
            ],
        ], 'storage');
    }

    /**
     * @test
     */
    public function it_processes_types_config()
    {
        $config = [
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
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'types' => [
                'document' => [
                    'storage' => 's3',
                    'max_file_size' => 4194304,
                    'mime_types' => ['application/pdf'],
                ],
                'image' => [
                    'storage' => 'local',
                    'max_file_size' => 8388608,
                    'mime_types' => ['image/jpg', 'image/png', 'image/gif'],
                ],
            ],
        ], 'types');
    }

    /**
     * @test
     */
    public function it_processes_glide_config()
    {
        $config = [
            'glide' => [
                'driver' => 'gd',
                'source' => 's3',
                'cache' => 'local',
                'max_image_size' => 4,
                'presets' => [
                    'small' => ['w' => 200, 'h' => 200, 'fit' => 'crop'],
                    'medium' => ['w' => 640, 'h' => 480, 'fit' => 'crop'],
                ],
                'defaults' => [
                    'fm' => 'png',
                    'q' => 75,
                ],
            ],
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'glide' => [
                'driver' => 'gd',
                'source' => 's3',
                'cache' => 'local',
                'group_cache_in_folders' => true,
                'max_image_size' => 4194304,
                'sign_key' => '%kernel.secret%',
                'presets' => [
                    'small' => ['w' => 200, 'h' => 200, 'fit' => 'crop'],
                    'medium' => ['w' => 640, 'h' => 480, 'fit' => 'crop'],
                ],
                'defaults' => [
                    'fm' => 'png',
                    'q' => 75,
                ],
            ],
        ], 'glide');
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
