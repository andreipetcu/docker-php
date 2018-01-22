<?php

namespace AndreiPetcu\DockerPhp;

use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

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
     * Docker constructor.
     *
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
     *
     * @return Processor
     */
    public function setPath(string $path): self
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
     *
     * @return Processor
     */
    public function setNamespace(string $namespace): self
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
    protected function tty(): self
    {
        $this->tty = true;

        return $this;
    }

    /**
     * @param string $command
     * @param array  $arguments
     * @param bool   $verbose
     *
     * @throws InvalidArgumentException
     * @throws ProcessFailedException
     *
     * @return Processor
     */
    protected function run(string $command, array $arguments, bool $verbose = false): self
    {
        if (!$this->path) {
            throw new InvalidArgumentException('You must provide a project path');
        }

        $process = $this->processBuilder->setPrefix($command)
            ->setWorkingDirectory($this->path)
            ->setArguments($arguments)
            ->getProcess();

        // Disable any kind of timeout
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        $process->setTty($this->tty);

        $callback = null;
        if ($verbose) {
            $callback = function ($type, $buffer) {
                unset($type);
                echo $buffer;
            };
        }

        $process->run($callback);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $this->output = $process->getOutput();

        return $this;
    }
}
