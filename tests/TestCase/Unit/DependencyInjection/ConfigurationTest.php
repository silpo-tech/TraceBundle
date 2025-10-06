<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use TraceBundle\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
    private Configuration $configuration;
    private Processor $processor;

    protected function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testValidConfiguration(): void
    {
        $config = [
            'trace' => [
                'id_header_name' => 'X-Some-Id',
                'id_log_extra_name' => 'someId',
                'autoconfigure_handlers' => false,
            ],
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, $config);

        $this->assertEquals('X-Some-Id', $processedConfig['id_header_name']);
        $this->assertEquals('someId', $processedConfig['id_log_extra_name']);
        $this->assertFalse($processedConfig['autoconfigure_handlers']);
    }

    public function testDefaultAutoconfigureHandlers(): void
    {
        $config = [
            'trace' => [
                'id_header_name' => 'X-Some-Id',
                'id_log_extra_name' => 'someId',
            ],
        ];

        $processedConfig = $this->processor->processConfiguration($this->configuration, $config);

        $this->assertTrue($processedConfig['autoconfigure_handlers']);
    }

    public function testMissingIdHeaderNameThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'trace' => [
                'id_log_extra_name' => 'someId',
            ],
        ];

        $this->processor->processConfiguration($this->configuration, $config);
    }

    public function testMissingIdLogExtraNameThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'trace' => [
                'id_header_name' => 'X-Some-Id',
            ],
        ];

        $this->processor->processConfiguration($this->configuration, $config);
    }

    public function testEmptyIdHeaderNameThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'trace' => [
                'id_header_name' => '',
                'id_log_extra_name' => 'someId',
            ],
        ];

        $this->processor->processConfiguration($this->configuration, $config);
    }

    public function testEmptyIdLogExtraNameThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'trace' => [
                'id_header_name' => 'X-Some-Id',
                'id_log_extra_name' => '',
            ],
        ];

        $this->processor->processConfiguration($this->configuration, $config);
    }
}
