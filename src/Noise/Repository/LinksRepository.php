<?php

namespace Noise\Repository;

class LinksRepository extends \Knp\Repository {

	public function getTableName() {
		return 'links';
	}

	public function findAll() {
		return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName());
	}

	public function findAllForSource($source) {
		return $this->db->fetchAll('SELECT links.id, destination, destinations.name AS name, reason FROM ' . $this->getTableName() . ' INNER JOIN machines AS sources ON links.source = sources.id INNER JOIN machines AS destinations ON links.destination = destinations.id WHERE source = ?', array($source));
	}

	public function findAllForDestination($destination) {
		return $this->db->fetchAll('SELECT links.id, source, sources.name AS name, reason FROM ' . $this->getTableName() . ' INNER JOIN machines AS sources ON links.source = sources.id INNER JOIN machines AS destinations ON links.destination = destinations.id WHERE destination = ?', array($destination));
	}
}