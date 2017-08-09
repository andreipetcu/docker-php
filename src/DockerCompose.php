<?php

namespace AndreiPetcu\DockerPhp;

use InvalidArgumentException;

class DockerCompose extends Processor
{
    const COMPOSE_COMMAND = 'docker-compose';
    const COMPOSE_START = 'up';
    const COMPOSE_RESTART = 'restart';
    const COMPOSE_STOP = 'stop';
    const COMPOSE_DOWN = 'down';
    const COMPOSE_BUILD = 'build';
    const COMPOSE_DAEMON = '-d';
    const COMPOSE_NAMESPACE = '-p';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Docker
     */
    protected $docker;

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

        return $this->run(self::COMPOSE_COMMAND, $arguments, $verbose);
    }

    /**
     * @param mixed $service
     * @return DockerCompose
     */
    public function restart($service = null, bool $verbose = false): DockerCompose
    {
        $service = ! is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_RESTART
            ],
            $service
        ));

        return $this->run(self::COMPOSE_COMMAND, $arguments, $verbose);
    }

    /**
     * @param mixed $service
     * @return DockerCompose
     */
    public function stop($service = null, bool $verbose = false): DockerCompose
    {
        $service = ! is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_STOP
            ],
            $service
        ));

        return $this->run(self::COMPOSE_COMMAND, $arguments, $verbose);
    }


    /**
     * @param mixed $service
     * @return DockerCompose
     */
    public function destroy($service = null, bool $verbose = false): DockerCompose
    {
        $service = ! is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_DOWN
            ],
            $service
        ));

        return $this->run(self::COMPOSE_COMMAND, $arguments, $verbose);
    }

    /**
     * @param mixed $service
     * @return DockerCompose
     */
    public function build($service = null, bool $verbose = false): DockerCompose
    {
        $service = ! is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_BUILD
            ],
            $service
        ));

        return $this->run(self::COMPOSE_COMMAND, $arguments, $verbose);
    }

    /**
     * @param Docker $docker
     */
    public function docker(Docker $docker): DockerCompose
    {
        $this->docker = $docker;

        return $this;
    }

    /**
     * @param string $service
     */
    public function ssh(string $service)
    {
        if (! $this->docker) {
            throw new InvalidArgumentException('You must provide a Docker instance');
        }

        $container = sprintf('%s_%s_1', $this->namespace, $service);

        $this->docker->ssh($container);
    }
}
