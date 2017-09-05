<?php

namespace Tests\Unit;

use AndreiPetcu\DockerPhp\Docker;
use InvalidArgumentException;
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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCodeText')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
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
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->start($service, true));
    }

    /**
     * @dataProvider startProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStartThrowsInvalidArgumentExceptionWhenNoPathIsGiven(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCodeText')
            ->once()
            ->andReturn('')
            ->shouldReceive('getWorkingDirectory')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isOutputDisabled')
            ->once()
            ->andReturn(true)
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(InvalidArgumentException::class);

        $this->assertSame($docker, $docker->start($service, true));
    }

    /**
     * @dataProvider restartProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testRestart(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'restart'],
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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->restart($service));
    }

    /**
     * @dataProvider restartProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testRestartVerbose(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'restart'],
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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->restart($service, true));
    }

    /**
     * @dataProvider restartProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testRestartThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'restart'],
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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->restart($service));
    }

    /**
     * @dataProvider restartProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testRestartVerboseThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'restart'],
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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->restart($service, true));
    }

    /**
     * @dataProvider stopProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStop(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'stop'],
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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->stop($service));
    }

    /**
     * @dataProvider stopProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStopVerbose(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'stop'],
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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->andReturnSelf()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->stop($service, true));
    }

    /**
     * @dataProvider stopProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStopThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'stop'],
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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->stop($service));
    }

    /**
     * @dataProvider stopProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testStopVerboseThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'stop'],
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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->stop($service, true));
    }

    /**
     * @dataProvider buildProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testBuild(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'build'],
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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->build($service));
    }

    /**
     * @dataProvider buildProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testBuildVerbose(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'build'],
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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->andReturnSelf()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->build($service, true));
    }

    /**
     * @dataProvider buildProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testBuildThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'build'],
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
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->build($service));
    }

    /**
     * @dataProvider buildProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testBuildVerboseThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['-p', 'docker', 'build'],
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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getExitCode')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->build($service, true));
    }

    /**
     * @dataProvider destroyProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testDestroy(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = ['-p', 'docker', 'down'];

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
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->andReturnSelf()
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->destroy());
    }

    /**
     * @dataProvider destroyProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testDestroyVerbose(ProcessBuilder $processBuilder, Process $process, $service)
    {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = ['-p', 'docker', 'down'];

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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('isSuccessful')
            ->once()
            ->andReturn(true)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->assertSame($docker, $docker->destroy(true));
    }

    /**
     * @dataProvider destroyProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testDestroyThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = ['-p', 'docker', 'down'];

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
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->destroy());
    }

    /**
     * @dataProvider destroyProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param $service
     * @internal param $services
     */
    public function testDestroyVerboseThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        $service
    ) {
        $docker = new DockerCompose($processBuilder);

        $workingDirectory = dirname(__DIR__, 1);

        $docker->setPath($workingDirectory);

        $arguments = ['-p', 'docker', 'down'];

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
            ->with(m::on(function ($callback) {
                $callback(Process::ERR, '');

                return is_callable($callback);
            }))
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setTty')
            ->once()
            ->with(false)
            ->shouldReceive('getCommandLine')
            ->once()
            ->andReturn('')
            ->shouldReceive('getOutput')
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
            ->andReturn(false)
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);

        $this->assertSame($docker, $docker->destroy(true));
    }

    /**
     * @dataProvider dockerProvider
     * @param ProcessBuilder $processBuilder
     * @param Docker $docker
     */
    public function testDocker(ProcessBuilder $processBuilder, Docker $docker)
    {
        $compose = new DockerCompose($processBuilder);

        $instance = $compose->docker($docker);

        $this->assertInstanceOf(DockerCompose::class, $instance);
    }

    /**
     * @dataProvider dockerProvider
     * @param ProcessBuilder $processBuilder
     * @param Docker $docker
     */
    public function testSsh(ProcessBuilder $processBuilder, Docker $docker)
    {
        $compose = new DockerCompose($processBuilder);
        $compose->docker($docker);

        $docker->shouldReceive('ssh')
            ->once()
            ->with('docker_service_1')
            ->andReturnSelf();

        $instance = $compose->ssh('service');

        $this->assertInstanceOf(DockerCompose::class, $instance);
    }

    /**
     * @dataProvider dockerProvider
     * @param ProcessBuilder $processBuilder
     * @param Docker $docker
     */
    public function testSshThrowsInvalidArgumentExceptionWhenNoDockerInstanceIsGiven(
        ProcessBuilder $processBuilder,
        Docker $docker
    ) {
        $this->expectException(InvalidArgumentException::class);
        $compose = new DockerCompose($processBuilder);
        $compose->ssh('service');
    }
}
