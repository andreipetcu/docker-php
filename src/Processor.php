<?php

namespace AndreiPetcu\DockerPhp;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Processor
{
    /**
     * @var ProcessBuilder
     */
    protected $processBuilder;

    /**
     * @var string
     */
    protected $namespace = 'docker';

    /**
     * @var bool;
     */
    protected $tty;

    /**
     * Docker constructor.
     * @param ProcessBuilder $processBuilder
     */
    public function __construct(ProcessBuilder $processBuilder)
    {
        $this->processBuilder = $processBuilder;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): Processor
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @return DockerCompose
     */
    protected function tty(): Processor
    {
        $this->tty = true;

        return $this;
    }

    /**
     * @param array $arguments
     * @return DockerCompose
     */
    protected function run(string $command, array $arguments, bool $verbose = false): Processor
    {
        $process = $this->processBuilder->setPrefix($command)
            ->setWorkingDirectory($this->path)
            ->setArguments($arguments)
            ->getProcess();

        if ($this->tty) {
            $process->setTty(true);
        }

        $callback = null;
        if ($verbose) {
            $callback = function ($type, $buffer) {
                unset($type);
                echo $buffer;
            };
        }

        $process->run($callback);

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }
}
