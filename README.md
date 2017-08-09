# Docker PHP

Docker PHP is a small wrapper for easy usage of docker/docker-compose written in PHP.

[![Latest Version](https://img.shields.io/github/release/andreipetcu/docker-php.svg?style=flat-square)](https://github.com/andreipetcu/docker-php/releases)
[![Build Status](https://travis-ci.org/andreipetcu/docker-php.svg?branch=master)](https://travis-ci.org/andreipetcu/docker-php)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


Installation
------------

```bash
composer require andreipetcu/docker-php:v0.2-alpha
```

Usage
-----
```php
<?php

use AndreiPetcu\DockerPhp\Docker;
use AndreiPetcu\DockerPhp\DockerCompose;
use Symfony\Component\Process\ProcessBuilder;

$compose = new DockerCompose(new ProcessBuilder());
$compose->setPath('/path/to/project')
    ->setNamespace('awesome');

$docker = new Docker(new ProcessBuilder());

// All commands accept either a service as a string or an array of services
// and a verbose flag which defaults to false
$compose->build('nginx', true)
    ->start('nginx', true)
    ->restart('nginx', true)
    ->stop('nginx', true)
    ->destroy('nginx', true);

// Will ssh into awesome_nginx_1
$compose->start('nginx')
    ->docker($docker)
    ->ssh('nginx');

// Will ssh into the given container.
$docker->ssh('container');
```

TODO
----
- Cover all code
- Remove duplicated code from DockerCompose (start, restart, stop, destroy, build)

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.