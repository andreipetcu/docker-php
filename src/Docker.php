<?php

namespace AndreiPetcu\DockerPhp;

class Docker extends Processor
{
    const DOCKER_COMMAND = 'docker';
    const DOCKER_EXEC = 'exec';
    const DOCKER_INTERACTIVE = '-i';
    const DOCKER_TTY = '-t';

    /**
     * @param $service
     */
    public function ssh(string $container)
    {
        // TODO: Find a more suitable fix
        $this->path = '/tmp';

        $arguments = [
            self::DOCKER_EXEC,
            self::DOCKER_INTERACTIVE,
            self::DOCKER_TTY,
            $container,
            'bash'
        ];

        $this->tty()->run(self::DOCKER_COMMAND, $arguments);
    }
}
