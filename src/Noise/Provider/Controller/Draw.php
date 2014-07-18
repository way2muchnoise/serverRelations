<?php

namespace Noise\Provider\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;

use Symfony\Component\Validator\Constraints as Assert;

class Draw implements ControllerProviderInterface {

	public function connect(Application $app) {

		// Create new ControllerCollection
		$controllers = $app['controllers_factory'];

		//Route for drawing a machines links
		$controllers
			->match('/{machineId}/', array($this, 'draw'))
			->assert('machineId', '\d+')
			->method('GET|POST')
			->bind('draw');

		return $controllers;
	}

	public function draw(Application $app, $machineId) {

		$machine = $app['db.machines']->findById($machineId);

		if($machine === false) {
			return $app->redirect($app['url_generator']->generate('machines.overview'));
		}

		$machineDraw = array(
				'type' => 'circle',
				'cx' => 250,
				'cy' => 250,
				'r' => 50
			);

		$machineName = array(
				'type' => 'text',
				'x' => 250,
				'y' => 250,
				'text' => $machine['name'],
				'font-size' => 16,
				'href' => $app['url_generator']->generate('machines.detail', array('machineId' => $machine['id']))
			);

		$json = array(
				$machineDraw,
				$machineName,
			);

		$sources = $app['db.links']->findAllForDestination($machineId);
		$destinations = $app['db.links']->findAllForSource($machineId);
		$links = array_merge($sources, $destinations);
		$count = count($links) == 0 ? 1 : count($links);
		$dg = 6.28 / $count;
		$g = 0;

		foreach ($sources as $link) {
			$rad = $dg * $g;
			$x = 250 + 200 * cos($rad);
			$y = 250 + 200 * sin($rad);
			$machineDraw = array(
					'type' => 'circle',
					'cx' => $x,
					'cy' => $y,
					'r' => 30,
				);
			$json[] = $machineDraw;
			$machineName = array(
					'type' => 'text',
					'x' => $x,
					'y' => $y,
					'text' => $link['name'],
					'font-size' => 12,
					'href' => $app['url_generator']->generate('machines.detail', array('machineId' => $link['source']))
				);
			$json[] = $machineName;
			$lineCX = 250 + 50 * cos($rad);
			$lineCY = 250 + 50 * sin($rad);
			$lineX = $x - 30 * cos($rad);
			$lineY = $y - 30 * sin($rad);
			$line = array(
					'type' => 'path',
					'path' => 'M' . $lineCX . ',' . $lineCY .'L' . $lineX .',' . $lineY,
					'stroke' => 'gray',
				);
			$json[] = $line;
			$arrowHead = array(
					'type' => 'circle',
					'cx' => $lineCX,
					'cy' => $lineCY,
					'r' => 2,
					'fill' => 'blue'
				);
			$json[] = $arrowHead;
			$reasonX = 250 + 80 * cos($rad);
			$reasonY = 250 + 80 * sin($rad);
			$reason = array(
					'type' => 'text',
					'x' => $reasonX,
					'y' => $reasonY,
					'text' => $link['reason'],
					'font-size' => 16,
				);
			$json[] = $reason;
			$g++;
		}

		foreach ($destinations as $link) {
			$rad = $dg * $g;
			$x = 250 + 200 * cos($rad);
			$y = 250 + 200 * sin($rad);
			$machineDraw = array(
					'type' => 'circle',
					'cx' => $x,
					'cy' => $y,
					'r' => 30,
				);
			$json[] = $machineDraw;
			$machineName = array(
					'type' => 'text',
					'x' => $x,
					'y' => $y,
					'text' => $link['name'],
					'font-size' => 12,
					'href' => $app['url_generator']->generate('machines.detail', array('machineId' => $link['destination']))
				);
			$json[] = $machineName;
			$lineCX = 250 + 50 * cos($rad);
			$lineCY = 250 + 50 * sin($rad);
			$lineX = $x - 30 * cos($rad);
			$lineY = $y - 30 * sin($rad);
			$line = array(
					'type' => 'path',
					'path' => 'M' . $lineCX . ',' . $lineCY .'L' . $lineX .',' . $lineY,
					'stroke' => 'gray',
				);
			$json[] = $line;
			$arrowHead = array(
				'type' => 'circle',
					'cx' => $lineX,
					'cy' => $lineY,
					'r' => 2,
					'fill' => 'blue'
				);
			$json[] = $arrowHead;
			$reasonX = 250 + 140 * cos($rad);
			$reasonY = 250 + 140 * sin($rad);
			$reason = array(
					'type' => 'text',
					'x' => $reasonX,
					'y' => $reasonY,
					'text' => $link['reason'],
					'font-size' => 16,
				);
			$json[] = $reason;
			$g++;
		}

		//var_dump($json);

		return $app['twig']->render('draw.twig', array(
			'machine' => $machine,
			'json' => json_encode($json),
			));
	}

}