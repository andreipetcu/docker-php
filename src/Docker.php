<?php

namespace AndreiPetcu\DockerPhp;

class Docker extends Processor
{
    const DOCKER_COMMAND = 'docker';
    const DOCKER_EXEC = 'exec';
    const DOCKER_NETWORK = 'network';
    const DOCKER_INTERACTIVE = '-i';
    const DOCKER_TTY = '-t';

    /**
     * @param $service
     */
    public function ssh(string $container): self
    {
        return $this->exec($container, 'bash', true);
    }

    /**
     * @param string $container
     * @param string $command
     * @param bool   $interactive
     */
    public function exec(string $container, string $command, bool $interactive = false): self
    {
        // TODO: Find a more suitable fix
        $this->path = sys_get_temp_dir();

        $arguments = [
            self::DOCKER_EXEC,
            $interactive ? self::DOCKER_INTERACTIVE : null,
            $interactive ? self::DOCKER_TTY : null,
            $container,
            $command,
        ];

        if ($interactive) {
            $this->tty();
        }

        $this->run(self::DOCKER_COMMAND, $arguments);

        return $this;
    }

    /**
     * @param string $action
     * @param array  $arguments
     *
     * @return Docker
     */
    public function network(string $action, array $arguments = []): self
    {
        $this->path = sys_get_temp_dir();

        $arguments = array_merge(
            [
                self::DOCKER_NETWORK,
                $action,
            ],
            $arguments
        );

        $this->run(self::DOCKER_COMMAND, $arguments);

        return $this;
    }
}
