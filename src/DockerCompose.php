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
     * @var Docker
     */
    protected $docker;

    /**
     * @param null $service
     * @param bool $verbose
     *
     * @return DockerCompose
     */
    public function start($service = null, bool $verbose = false): self
    {
        $service = !is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_START, self::COMPOSE_DAEMON,
            ],
            $service
        ));

        $this->fire($arguments, $verbose);

        return $this;
    }

    /**
     * @param null $service
     * @param bool $verbose
     *
     * @return DockerCompose
     */
    public function restart($service = null, bool $verbose = false): self
    {
        $service = !is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_RESTART,
            ],
            $service
        ));

        $this->fire($arguments, $verbose);

        return $this;
    }

    /**
     * @param null $service
     * @param bool $verbose
     *
     * @return DockerCompose
     */
    public function stop($service = null, bool $verbose = false): self
    {
        $service = !is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_STOP,
            ],
            $service
        ));

        $this->fire($arguments, $verbose);

        return $this;
    }

    /**
     * @param null $service
     * @param bool $verbose
     *
     * @return DockerCompose
     */
    public function destroy(bool $verbose = false): self
    {
        $arguments = [
            self::COMPOSE_NAMESPACE, $this->namespace,
            self::COMPOSE_DOWN,
        ];

        $this->fire($arguments, $verbose);

        return $this;
    }

    /**
     * @param null $service
     * @param bool $verbose
     *
     * @return DockerCompose
     */
    public function build($service = null, bool $verbose = false): self
    {
        $service = !is_array($service) ? [$service] : $service;

        $arguments = array_filter(array_merge(
            [
                self::COMPOSE_NAMESPACE, $this->namespace,
                self::COMPOSE_BUILD,
            ],
            $service
        ));

        $this->fire($arguments, $verbose);

        return $this;
    }

    /**
     * @param null $service
     * @param bool $verbose
     *
     * @return DockerCompose
     */
    public function docker(Docker $docker): self
    {
        $this->docker = $docker;

        return $this;
    }

    /**
     * @param string $service
     *
     * @throws InvalidArgumentException
     */
    public function ssh(string $service): self
    {
        if (!$this->docker) {
            throw new InvalidArgumentException('You must provide a Docker instance');
        }

        $container = sprintf('%s_%s_1', $this->namespace, $service);

        $this->docker->ssh($container);

        return $this;
    }

    /**
     * @param array $arguments
     * @param bool  $verbose
     */
    protected function fire(array $arguments, bool $verbose = false): self
    {
        $this->run(self::COMPOSE_COMMAND, $arguments, $verbose);

        return $this;
    }
}
