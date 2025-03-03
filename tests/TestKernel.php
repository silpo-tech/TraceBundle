<?php

declare(strict_types=1);

namespace TraceBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\HttpKernel\Kernel;
use TraceBundle\TraceBundle;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public const array DEFAULT_BUNDLES = [
        FrameworkBundle::class,
        TraceBundle::class
    ];

    public const array DEFAULT_CONFIGS = [
        __DIR__.'/Resources/config/framework.yaml'
    ];

    public const array MESSAGE_BUS_CONFIGS = [
        __DIR__.'/Resources/config/framework.yaml',
        __DIR__.'/Resources/config/enqueue.yaml',
    ];

    public function __construct(
        string $environment,
        bool $debug,
        /** @var string[] */
        protected iterable $testBundle = self::DEFAULT_BUNDLES,
        /** @var string[]|callable[] */
        protected iterable $testConfigs = self::DEFAULT_CONFIGS
    )
    {
        parent::__construct($environment, $debug);
    }

    public function addTestBundle(string $bundleClassName): void
    {
        $this->testBundle[] = $bundleClassName;
    }

    public function addTestConfig(string|callable $config): void
    {
        $this->testConfigs[] = $config;
    }

    public function getConfigDir(): string
    {
        return $this->getProjectDir() . '/src/Resources/config';
    }

    public function getCacheDir(): string
    {
        return __DIR__.'/../var/cache/'.$this->getEnvironment();
    }

    public function getLogDir(): string
    {
        return __DIR__.'/../var/log';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        foreach ($this->testConfigs as $config) {
            $loader->load($config);
        }
    }

    public function registerBundles(): iterable
    {
        $this->testBundle = array_unique($this->testBundle);

        foreach ($this->testBundle as $bundle) {
            yield new $bundle();
        }
    }

    public function handleOptions(array $options): void
    {
        if (array_key_exists('config', $options) && is_callable($configCallable = $options['config'])) {
            $configCallable($this);
        }
    }

    public function shutdown(): void
    {
        parent::shutdown();

        $cacheDirectory = $this->getCacheDir();
        $logDirectory = $this->getLogDir();

        $filesystem = new Filesystem();

        if ($filesystem->exists($cacheDirectory)) {
            $filesystem->remove($cacheDirectory);
        }

        if ($filesystem->exists($logDirectory)) {
            $filesystem->remove($logDirectory);
        }
    }

    public function getTagged(string $tag): array
    {
        $container = $this->getContainerBuilder();
        return $container->findTaggedServiceIds($tag);
    }

    public function getContainerBuilder() : ContainerBuilder
    {
        return parent::getContainerBuilder();
    }
}