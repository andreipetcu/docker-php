<?php

namespace Tests\Unit;

use Mockery as m;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

trait ProcessorProvider
{
    public function isInstantiableProvider()
    {
        $processBuilder = m::mock(ProcessBuilder::class);

        return [
            [$processBuilder]
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
