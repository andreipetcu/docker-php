# Docker PHP

Docker PHP is a small wrapper for easy usage of docker/docker-compose written in PHP.

[![Latest Version](https://img.shields.io/github/release/andreipetcu/docker-php.svg?style=flat-square)](https://github.com/andreipetcu/docker-php/releases)
[![Build Status](https://travis-ci.org/andreipetcu/docker-php.svg?branch=master)](https://travis-ci.org/andreipetcu/docker-php)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)


Installation
------------

```bash
composer require andreipetcu/docker-php
```

Usage
-----
```php
<?php

use AndreiPetcu\DockerPhp\DockerCompose;
use Symfony\Component\Process\ProcessBuilder;

$dockerCompose = new DockerCompose(new ProcessBuilder);

$dockerCompose->setPath('/path/to/project');

// Either give it one service
$dockerCompose->start('nginx');

// Or for multiple services
$dockerCompose->start(['nginx', 'mysql']);

// Or finally for all services
$dockerCompose->start();

// If you want to enable ouput
$dockerCompose->start('nginx', true); 
```

License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.