<?php

namespace Noise\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\Validator\Constraints as Assert;

class Links implements ControllerProviderInterface {

	public function connect(Application $app) {

		// Create new ControllerCollection
		$controllers = $app['controllers_factory'];

		$controllers
			->match('/add/', array($this, 'add'))
			->method('GET|POST')
			->bind('links.add');

		$controllers
			->match('/{id}/delete/', array($this, 'delete'))
			->assert('id', '\d+')
			->method('POST')
			->bind('links.delete');

		$controllers
			->match('/{id}/', array($this, 'detail'))
			->assert('id', '\d+')
			->method('GET|POST')
			->bind('links.edit');

		return $controllers;

	}

	public function delete(Application $app, $id) {

		$app['db.links']->delete(array('id' => $id));
		return $app->redirect($app['url_generator']->generate('machines.detail', array('machineId' => (int)$_POST['origin'])) . '?feedback=deleted');
	}

	public function add(Application $app) {

		$source = $app['request']->get('source');
		$destination = $app['request']->get('destination');
		$origin = $app['request']->get('origin');

			$raw = $app['db.machines']->findAll();
			$machines = array();
			foreach ($raw as $r) {
				$machines[$r['id']] = $r['name'];
			}

		$form = $app['form.factory']
					->createNamed('form', 'form', array('source' => $source, 'destination' => $destination, 'origin' => $origin))
					->add('source', 'choice', array(
						'label' => 'Source',
						'choices' => $machines,
						'constraints' => array(new Assert\NotBlank()),
						'empty_value' => 'Choose a machine',
						))
					->add('destination', 'choice', array(
						'label' => 'Destination',
						'choices' => $machines,
						'constraints' => array(new Assert\NotBlank()),
						'empty_value' => 'Choose a machine',
						))
					->add('reason', 'text', array(
						'label' => 'Link reason',
						'constraints' => array(new Assert\NotBlank()),
						))
					->add('nota', 'textarea', array(
						'label' => 'Nota'
						))
					->add('origin', 'hidden', array(
					));

			// Form was submitted: process it
			if ('POST' == $app['request']->getMethod()) {
				$form->bind($app['request']);
				if ($form->isValid()) {
					$data=$form->getData();
					$origin = $data['origin'];
					unset($data['origin']);
					$app['db.links']->insert($data);
					if($origin == '') {
						return $app->redirect($app['url_generator']->generate('links.add') . '?feedback=added');
					}
					return $app->redirect($app['url_generator']->generate('machines.detail', array('machineId' => $origin)). '?feedback=added');
				}
			}

		return $app['twig']->render('links.twig', array(
			'form' => $form->createView(),
			'feedback' => $app['request']->get('feedback'),
			'state' => 'Add'
		));
	}

	public function detail(Application $app, $id) {

		$link = $app['db.links']->findById($id);
		if ($link === false) {
			return $app->redirect($app['url_generator']->generate('links.add'));
		}
		$origin = $app['request']->get('origin');

		$raw = $app['db.machines']->findAll();
			$machines = array();
			foreach ($raw as $r) {
				$machines[$r['id']] = $r['name'];
			}

		$form = $app['form.factory']
					->createNamed('form', 'form', array_merge($link,array('origin' => $origin)))
					->add('source', 'choice', array(
						'label' => 'Source',
						'choices' => $machines,
						'constraints' => array(new Assert\NotBlank()),
						'empty_value' => 'Choose a machine',
						))
					->add('destination', 'choice', array(
						'label' => 'Destination',
						'choices' => $machines,
						'constraints' => array(new Assert\NotBlank()),
						'empty_value' => 'Choose a machine',
						))
					->add('reason', 'text', array(
						'label' => 'Link reason',
						'constraints' => array(new Assert\NotBlank()),
						))
					->add('nota', 'textarea', array(
						'label' => 'Nota'
						))
					->add('origin', 'hidden', array(
					));

			// Form was submitted: process it
			if ('POST' == $app['request']->getMethod()) {
				$form->bind($app['request']);
				if ($form->isValid()) {
					$data=$form->getData();
					$origin = $data['origin'];
					unset($data['origin']);
					$app['db.links']->update($data, array('id' => $id));
					if($origin == '') {
						return $app->redirect($app['url_generator']->generate('links.add') . '?feedback=edited');
					}
					return $app->redirect($app['url_generator']->generate('machines.detail', array('machineId' => $origin)). '?feedback=edited');
				}
			}

			return $app['twig']->render('links.twig', array(
			'form' => $form->createView(),
			'feedback' => $app['request']->get('feedback'),
			'state' => 'Edit',
			'link' => $link
		));
	}

}