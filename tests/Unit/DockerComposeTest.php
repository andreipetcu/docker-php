<?php

namespace Tests\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use AndreiPetcu\DockerPhp\DockerCompose;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerComposeTest extends TestCase
{
    use DockerComposeProvider;

    /**
     * @dataProvider isInstantiableProvider
     * @param ProcessBuilder $processBuilder
     */
    public function testIsInstantiable(ProcessBuilder $processBuilder)
    {
        $docker = new DockerCompose($processBuilder);

        $this->assertInstanceOf(DockerCompose::class, $docker);
    }

    /**
     * @dataProvider getPathProvider
     * @param ProcessBuilder $processBuilder
     * @param string $path
     */
    public function testGetPath(ProcessBuilder $processBuilder, string $path)
    {
        $docker = new DockerCompose($processBuilder);

        $this->assertSame($path, $docker->setPath($path)->getPath());
    }

    /**
     * @dataProvider getNamespaceProvider
     * @param ProcessBuilder $processBuilder
     * @param string $namespace
     */
    public function testGetNamespace(ProcessBuilder $processBuilder, string $namespace)
    {
        $docker = new DockerCompose($processBuilder);

        $this->assertSame($namespace, $docker->setNamespace($namespace)->getNamespace());
    }

    /**
     * @dataProvider startProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStart(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'up', '-d'],
            is_string($service) ? [$service] : $service
        );

        $processBuilder->shouldReceive('setPrefix')
            ->once()
            ->with('docker-compose')
            ->andReturnSelf()
            ->shouldReceive('setWorkingDirectory')
            ->once()
            ->with($workingDirectory)
            ->andReturnSelf()
            ->shouldReceive('setArguments')
            ->once()
            ->with($arguments)
            ->andReturnSelf()
            ->shouldReceive('getProcess')
            ->andReturn($process);

        $process->shouldReceive('run')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true);

        $this->assertSame($docker, $docker->start($service));
    }

    /**
     * @dataProvider startProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStartVerbose(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'up', '-d'],
            is_string($service) ? [$service] : $service
        );

        $processBuilder->shouldReceive('setPrefix')
            ->once()
            ->with('docker-compose')
            ->andReturnSelf()
            ->shouldReceive('setWorkingDirectory')
            ->once()
            ->with($workingDirectory)
            ->andReturnSelf()
            ->shouldReceive('setArguments')
            ->once()
            ->with($arguments)
            ->andReturnSelf()
            ->shouldReceive('getProcess')
            ->andReturn($process);

        $process->shouldReceive('run')
            ->once()
            ->with(m::on(function($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->andReturnSelf()
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true);

        $this->assertSame($docker, $docker->start($service, true));
    }

    /**
     * @dataProvider startProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStartThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'up', '-d'],
            is_string($service) ? [$service] : $service
        );

        $processBuilder->shouldReceive('setPrefix')
            ->once()
            ->with('docker-compose')
            ->andReturnSelf()
            ->shouldReceive('setWorkingDirectory')
            ->once()
            ->with($workingDirectory)
            ->andReturnSelf()
            ->shouldReceive('setArguments')
            ->once()
            ->with($arguments)
            ->andReturnSelf()
            ->shouldReceive('getProcess')
            ->andReturn($process);

        $process->shouldReceive('run')
            ->with(null)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCodeText')
            ->once()
            ->andReturn('')
            ->shouldReceive('getWorkingDirectory')
            ->once()
            ->andReturn('')
            ->shouldReceive('isOutputDisabled')
            ->once()
            ->andReturn(true)
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(false);

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->start($service));
    }

    /**
     * @dataProvider startProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStartVerboseThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'up', '-d'],
            is_string($service) ? [$service] : $service
        );

        $processBuilder->shouldReceive('setPrefix')
            ->once()
            ->with('docker-compose')
            ->andReturnSelf()
            ->shouldReceive('setWorkingDirectory')
            ->once()
            ->with($workingDirectory)
            ->andReturnSelf()
            ->shouldReceive('setArguments')
            ->once()
            ->with($arguments)
            ->andReturnSelf()
            ->shouldReceive('getProcess')
            ->andReturn($process);

        $process->shouldReceive('run')
            ->with(m::on(function($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCodeText')
            ->once()
            ->andReturn('')
            ->shouldReceive('getWorkingDirectory')
            ->once()
            ->andReturn('')
            ->shouldReceive('isOutputDisabled')
            ->once()
            ->andReturn(true)
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(false);

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->start($service, true));
    }
}
