<?php

namespace Noise\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\Validator\Constraints as Assert;

class Home implements ControllerProviderInterface {

	public function connect(Application $app) {

		// Create new ControllerCollection
		$controllers = $app['controllers_factory'];

		// Homepage
		$controllers
			->match('/', array($this, 'home'))
			->method('GET|POST')
			->bind('index');

		return $controllers;

	}

	public function home(Application $app) {
		return $app['twig']->render('home.twig', array(
			'feedback' => $app['request']->get('feedback'),
		));
	}
}