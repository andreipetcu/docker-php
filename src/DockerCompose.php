<?php

namespace AndreiPetcu\DockerPhp;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DockerCompose
{
    const COMMAND = 'docker-compose';
    const COMPOSE_START = 'up';
    const COMPOSE_DAEMON = '-d';
    const COMPOSE_NAMESPACE = '-p';

    /**
     * @var ProcessBuilder
     */
    protected $processBuilder;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $namespace = 'docker';

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
     * @return DockerCompose
     */
    public function setPath(string $path): DockerCompose
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
     */
    public function setNamespace(string $namespace): DockerCompose
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param mixed $service
     * @return DockerCompose
     */
    public function start($service = null, bool $verbose = false): DockerCompose
    {
        $service = ! is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_START, self::COMPOSE_DAEMON
            ],
            $service
        ));

        $this->run($arguments, $verbose);

        return $this;
    }

    /**
     * @param array $arguments
     * @return DockerCompose
     */
    protected function run(array $arguments, bool $verbose = false): DockerCompose
    {
        $process = $this->processBuilder->setPrefix(self::COMMAND)
            ->setWorkingDirectory($this->path)
            ->setArguments($arguments)
            ->getProcess();

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
