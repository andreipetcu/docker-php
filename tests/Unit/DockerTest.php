<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use AndreiPetcu\DockerPhp\Docker;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerTest extends TestCase
{
    use DockerProvider;

    /**
     * @dataProvider sshProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param string $container
     */
    public function testSsh(ProcessBuilder $processBuilder, Process $process, string $container)
    {
        $docker = new Docker($processBuilder);

        $workingDirectory = sys_get_temp_dir();

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['exec', '-i', '-t'],
            is_string($container) ? [$container] : $container,
            ['bash']
        );

        $processBuilder->shouldReceive('setPrefix')
            ->once()
            ->with('docker')
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
            ->with(true)
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
            ->andReturn(true)
            ->shouldReceive('getOutput')
            ->once()
            ->andReturn('')
            ->shouldReceive('stop')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $instance = $docker->ssh('container');

        $this->assertInstanceOf(Docker::class, $instance);
    }

    /**
     * @dataProvider sshProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param string $container
     */
    public function testSshThrowsProcessFailedExceptionWhenProcessFails(
        ProcessBuilder $processBuilder,
        Process $process,
        string $container
    ) {
        $docker = new Docker($processBuilder);

        $workingDirectory = sys_get_temp_dir();

        $docker->setPath($workingDirectory);

        $arguments = array_merge(
            ['exec', '-i', '-t'],
            is_string($container) ? [$container] : $container,
            ['bash']
        );

        $processBuilder->shouldReceive('setPrefix')
            ->once()
            ->with('docker')
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
            ->with(true)
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
            ->shouldReceive('stop')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setIdleTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf()
            ->shouldReceive('setTimeout')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->expectException(ProcessFailedException::class);
        $docker->ssh('container');
    }

    /**
     * @dataProvider networkProvider
     * @param ProcessBuilder $processBuilder
     * @param Process $process
     * @param string $container
     */
    public function testNetwork(ProcessBuilder $processBuilder, Process $process, string $container)
    {
        $docker = new Docker($processBuilder);

        $workingDirectory = sys_get_temp_dir();

        $docker->setPath($workingDirectory);

        $arguments = ['network', 'ls'];

        $processBuilder->shouldReceive('setPrefix')
                       ->once()
                       ->with('docker')
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
                ->shouldReceive('getWorkingDirectory')
                ->once()
                ->andReturn('')
                ->shouldReceive('isOutputDisabled')
                ->once()
                ->andReturn(true)
                ->shouldReceive('isSuccessful')
                ->once()
                ->andReturn(true)
                ->shouldReceive('getOutput')
                ->once()
                ->andReturn('')
                ->shouldReceive('stop')
                ->once()
                ->andReturnSelf()
                ->shouldReceive('setIdleTimeout')
                ->once()
                ->with(null)
                ->andReturnSelf()
                ->shouldReceive('setTimeout')
                ->once()
                ->with(null)
                ->andReturnSelf();

        $instance = $docker->network('ls');

        $this->assertInstanceOf(Docker::class, $instance);
    }
}
