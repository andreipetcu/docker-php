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
    public function start($service): DockerCompose
    {
        $service = is_string($service) ? [$service] : $service;

        $arguments = array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_START, self::COMPOSE_DAEMON
            ],
            $service
        );

        $this->run($arguments);

        return $this;
    }

    /**
     * @param array $arguments
     * @return DockerCompose
     */
    protected function run(array $arguments): DockerCompose
    {
        $process = $this->processBuilder->setPrefix(self::COMMAND)
            ->setWorkingDirectory($this->path)
            ->setArguments($arguments)
            ->getProcess();

        $process->run(function ($type, $buffer) {
            echo $buffer;
        });

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }
}
