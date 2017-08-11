<?php

namespace AndreiPetcu\DockerPhp;

use InvalidArgumentException;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Processor
{
    /**
     * @var string
     */
    protected $path;

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
    protected $tty = false;

    /**
     * @var string
     */
    protected $output = '';

    /**
     * @var int
     */
    protected $timeout = 600;

    /**
     * @var int
     */
    protected $idleTimeout = 600;

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
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Processor
     */
    public function setPath(string $path): Processor
    {
        $this->path = $path;

        return $this;
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
     * @return Processor
     */
    public function setNamespace(string $namespace): Processor
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @return Processor
     */
    protected function tty(): Processor
    {
        $this->tty = true;

        return $this;
    }

    /**
     * @param string $command
     * @param array $arguments
     * @param bool $verbose
     * @return Processor
     * @throws InvalidArgumentException
     * @throws ProcessFailedException
     */
    protected function run(string $command, array $arguments, bool $verbose = false): Processor
    {
        if (! $this->path) {
            throw new InvalidArgumentException('You must provide a project path');
        }

        $process = $this->processBuilder->setPrefix($command)
            ->setWorkingDirectory($this->path)
            ->setArguments($arguments)
            ->getProcess();

        $process->setTty($this->tty);

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

        $this->output = $process->getOutput();

        return $this;
    }
}
