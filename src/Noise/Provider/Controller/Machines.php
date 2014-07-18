<?php

namespace Noise\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\Validator\Constraints as Assert;

class Machines implements ControllerProviderInterface {

	public function connect(Application $app) {

		// Create new ControllerCollection
		$controllers = $app['controllers_factory'];

		//Overview of all machines
		$controllers
			->match('/', array($this, 'overview'))
			->method('GET|POST')
			->bind('machines.overview');

		//Detail of a machine
		$controllers
			->match('/{machineId}/', array($this, 'detail'))
			->assert('machineId', '\d+')
			->method('GET|POST')
			->bind('machines.detail');

		//Adding a machine
		$controllers
			->match('/add/', array($this, 'add'))
			->method('POST')
			->bind('machines.add');

		//Deleting a machine
		$controllers
			->match('/{machineId}/delete/', array($this, 'delete'))
			->assert('machineId', '\d+')
			->method('POST')
			->bind('machines.delete');

		return $controllers;

	}

	public function overview(Application $app) {

		$searched = isset($_GET['searched']) ? $_GET['searched'] : "";

		$machines = $app['db.machines']->find($searched);

		return $app['twig']->render('overview.twig', array(
			'machines' => $machines,
			'searched' => $searched,
			'feedback' => $app['request']->get('feedback'),
		));
	}

	public function detail(Application $app, $machineId) {

		$machine =  $app['db.machines']->findById($machineId);

		if($machine === false) {
			return $app->redirect($app['url_generator']->generate('machines.overview'));
		}

		$destinations = $app['db.links']->findAllForSource($machineId);
		$sources = $app['db.links']->findAllForDestination($machineId);

		return $app['twig']->render('detail.twig', array(
			'machine' => $machine,
			'destinations'=> $destinations,
			'sources' => $sources,
			'feedback' => $app['request']->get('feedback'),
		));
	}

	public function add(Application $app) {
		if (isset($_POST['machineName']) && $_POST['machineName'] != "") {
			if($app['db.machines']->findExact($_POST['machineName']) === false) {
				$app['db.machines']->insert(array('name' => $_POST['machineName']));
				return $app->redirect($app['url_generator']->generate('index') . '?feedback=added');
			}
			else{
				return $app->redirect($app['url_generator']->generate('index') . '?feedback=used');
			}
		}

		return $app->redirect($app['url_generator']->generate('index'));
	}

	public function delete(Application $app, $machineId) {
		$links = array_merge($app['db.links']->findAllForDestination($machineId), $app['db.links']->findAllForSource($machineId));
		foreach ($links as $link) {
			$app['db.links']->delete(array('id' => $link['id']));
		}
		$app['db.machines']->delete(array('id' => $machineId));
		return $app->redirect($app['url_generator']->generate('machines.overview') . '?feedback=deleted');
	}
}