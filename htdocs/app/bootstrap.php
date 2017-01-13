<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;


//\Tracy\Debugger::enable(\Tracy\Debugger::PRODUCTION);
//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log',"webmaster@albixon.cz");

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');
//new \Albixon\HttpAuth();

$container = $configurator->createContainer();

return $container;
