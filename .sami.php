<?php

/*
 * Copyright (c) 2017 ScientiaMobile Inc.
 */

/**
 * Configuration file for Sami: an API documentation generator
 * @see https://github.com/FriendsOfPHP/Sami
 *
 * To generate the API documentation:
 * - php bin/sami.phar update .sami
 *
 * The documentation will be generated in the docs folder
 *
 * Note: Sami requires PHP 7
 */

use Sami\Sami;
use Sami\Parser\Filter\FilterInterface;
use Sami\Reflection\ClassReflection;
use Sami\Reflection\MethodReflection;
use Sami\Reflection\PropertyReflection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude(['Cache', 'HttpClient'])
    ->in(__DIR__ . '/src')
;

return new Sami($iterator, array(
    'title'                => 'WURFL Microservice API',
    'build_dir'            => __DIR__.'/docs',
    'cache_dir'            => __DIR__.'/cache',
    'filter' => function () {
        return new class extends \Sami\Parser\Filter\DefaultFilter
        {

            public function acceptMethod(MethodReflection $method)
            {
                if ($method->getTags('internal'))  {
                    return false;
                }
                return parent::acceptMethod($method);
            }

        };
    }
));
