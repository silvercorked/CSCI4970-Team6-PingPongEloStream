<?php

use Doctum\Doctum;
use Doctum\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$dir = '../';
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
	->in($dir);

return new Doctum($iterator, [
	'title'			=> 'CSCI 4970 Ping Pong Elo Stream API',
	'language'		=> 'en',
	'build_dir'		=> __DIR__ . '/../docs',
	'cache_dir'		=> __DIR__ . '/../docs/cache',
	'source_dir'		=> dirname($dir),
	'remote_repository'	=> new GitHubRemoteRepository(
		'silvercorked/CSCI4970-Team6-PingPongEloStream',
		dirname($dir)
	),
]);

