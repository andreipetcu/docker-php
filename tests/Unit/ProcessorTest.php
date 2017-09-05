<?php

namespace Tests\Unit;

use AndreiPetcu\DockerPhp\Processor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class ProcessorTest extends TestCase
{
    use ProcessorProvider;

    /**
     * @dataProvider isInstantiableProvider
     * @param ProcessBuilder $processBuilder
     */
    public function testIsInstantiable(ProcessBuilder $processBuilder)
    {
        $processor = new Processor($processBuilder);

        $this->assertInstanceOf(Processor::class, $processor);
    }

    /**
     * @dataProvider getPathProvider
     * @param ProcessBuilder $processBuilder
     * @param string $path
     */
    public function testGetPath(ProcessBuilder $processBuilder, string $path)
    {
        $processor = new Processor($processBuilder);

        $this->assertSame($path, $processor->setPath($path)->getPath());
    }

    /**
     * @dataProvider getNamespaceProvider
     * @param ProcessBuilder $processBuilder
     * @param string $namespace
     */
    public function testGetNamespace(ProcessBuilder $processBuilder, string $namespace)
    {
        $processor = new Processor($processBuilder);

        $this->assertSame($namespace, $processor->setNamespace($namespace)->getNamespace());
    }

    /**
     * @dataProvider getOutputProvider
     * @param ProcessBuilder $processBuilder
     * @param string $output
     */
    public function testGetOutput(ProcessBuilder $processBuilder, string $output)
    {
        $processor = new Processor($processBuilder);

        $this->assertSame($output, $processor->getOutput());
    }
}
