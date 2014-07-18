<?php

namespace Noise\Repository;

class MachinesRepository extends \Knp\Repository {

	public function getTableName() {
		return 'machines';
	}

	public function findAll() {
		return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName());
	}

	public function find($term) {
		return $this->db->fetchAll('SELECT * FROM ' . $this->getTableName() . ' WHERE name LIKE ' . $this->db->quote('%'.$term.'%', \PDO::PARAM_STR));
	}

	public function findExact($term) {
		return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE name = ?', array($term));
	}

	public function findById($id) {
		return $this->db->fetchAssoc('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ?', array($id));
	}
}