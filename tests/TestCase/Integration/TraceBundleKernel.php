<?php

declare(strict_types=1);

namespace TraceBundle\Tests\TestCase\Integration;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use TraceBundle\TraceBundle;

class TraceBundleKernel extends Kernel
{
    public function __construct(string $environment, bool $debug, protected array $extraConfigs = [])
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TraceBundle(),
        ];
    }

    /**
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        foreach ($this->extraConfigs as $extraConfig) {
            $loader->load($extraConfig);
        }
    }

    public static function create(array $configs = []): self
    {
        return new self('test', true, $configs);
    }
}
