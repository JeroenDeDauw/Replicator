<?php

namespace Tests\Queryr\Replicator\Fixtures;

use PDO;

class TestFixtureFactory {

	public static function newInstance() {
		return new self();
	}

	public function newPDO() {
		return new PDO(
			'mysql:dbname=replicator_tests;host=localhost',
			'replicator_tests',
			'mysql_is_evil'
		);
	}

}