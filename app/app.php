<?php

// Bootstrap
require __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

$app->error(function (\Exception $e, $code) {
	if ($code == 404) {
		return '404 - Not Found! // ' . $e->getMessage();
	} else {
		return 'Shenanigans! Something went horribly wrong // ' . $e->getMessage();
	}
});

// Mount our Controllers
$app->mount('/', new Noise\Provider\Controller\Home());
$app->mount('/machines/', new Noise\Provider\Controller\Machines());
$app->mount('/links/', new Noise\Provider\Controller\Links());
$app->mount('/draw/', new Noise\Provider\Controller\Draw());