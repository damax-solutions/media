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
        $config = [
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'key_length' => 8,
            'types' => [],
        ]);
    }

    /**
     * @test
     */
    public function it_processes_config()
    {
        $config = [
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
        ];

        $this->assertProcessedConfigurationEquals([$config], [
            'key_length' => 12,
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
        ]);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }
}
