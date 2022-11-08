<?php

use Doctum\Doctum;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('.git')
    ->exclude('.vagrant')
    ->exclude('lang')
    ->exclude('resources')
    ->exclude('documentation')
    ->exclude('storage')
    ->exclude('vendor')
    ->in('../');

return new Doctum($iterator);

