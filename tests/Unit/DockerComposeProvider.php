<?php

namespace Tests\Unit;

use Mockery as m;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

trait DockerComposeProvider
{
    public function isInstantiableProvider()
    {
        $processBuilder = m::mock(ProcessBuilder::class);

        return [
            [$processBuilder]
        ];
    }

    public function startProvider()
    {
        $processBuilder = m::mock(ProcessBuilder::class);
        $process = m::mock(Process::class);
        $process->shouldReceive('stop');

        return [
            'with single service' => [$processBuilder, $process, 'service'],
            'with multiple services' => [$processBuilder, $process, ['service1', 'service2']],
        ];
    }

    public function getPathProvider()
    {
        $processBuilder = m::mock(ProcessBuilder::class);

        return [
            [$processBuilder, ''],
            [$processBuilder, '/path/to/nowhere'],
        ];
    }

    public function getNamespaceProvider()
    {
        $processBuilder = m::mock(ProcessBuilder::class);

        return [
            [$processBuilder, ''],
            [$processBuilder, 'superman'],
        ];
    }
}
