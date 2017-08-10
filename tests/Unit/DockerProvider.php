<?php

namespace Tests\Unit;

use Mockery as m;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

trait DockerProvider
{
    public function sshProvider()
    {
        return [
            [m::mock(ProcessBuilder::class), m::mock(Process::class), 'container']
        ];
    }
}
