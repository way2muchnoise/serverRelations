<?php

// Require Composer Autoloader
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Create new Silex App
$app = new Silex\Application();

// App Configuration
$app['debug'] = true;
if($app['debug']) {
	$app->register(new Whoops\Provider\Silex\WhoopsServiceProvider);
}

// Use Twig — @note: Be sure to install Twig via Composer first!
$app->register(new Silex\Provider\TwigServiceProvider(), array(
'twig.path' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'views'
));

// Use Doctrine — @note: Be sure to install Doctrine via Composer first!
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
	'db.options' => array(
	'driver'	=> 'pdo_mysql',
	'host'	=> 'localhost',
	'dbname'	=> 'servers',
	'user'	=> 'root',
	'password'	=> '',
	'charset'	=> 'utf8',
	'driverOptions' => array(
		1002 => 'SET NAMES utf8'
		)
	)
));

// Use Repository Service Provider — @note: Be sure to install KNP RSP via Composer first!
$app->register(new Knp\Provider\RepositoryServiceProvider(), array(
	'repository.repositories' => array(
		'db.machines' => 'Noise\\Repository\\MachinesRepository',
		'db.links' => 'Noise\\Repository\\LinksRepository',
	)
));

// Use UrlGeneratorServiceProvider
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Use SessionGeneratorServiceProvider
$app->register(new Silex\Provider\SessionServiceProvider());

// Use Validator Service Provider - @note: Be sure to install "symfony/validator" via Composer first!
$app->register(new Silex\Provider\ValidatorServiceProvider());

// Use Form Service Provider - @note: Be sure to install "symfony/form" & "symfony/twig-bridge" via Composer first!
$app->register(new Silex\Provider\FormServiceProvider());

// Use Translation Service Provider because without it our form won't work
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
'translator.messages' => array(),
));