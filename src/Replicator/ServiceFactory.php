<?php

namespace QueryR\Replicator;

use PDO;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\Dump\Store\StoreInstaller;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ServiceFactory {

	public static function newForInstaller( PDO $pdo, $dbName ) {
		return new self( $pdo, $dbName );
	}

	private $pdo;
	private $dbName;

	private function __construct( PDO $pdo, $dbName ) {
		$this->pdo = $pdo;
		$this->dbName = $dbName;
	}

	public function newQueryEngineInstaller() {
		$sqlStore = new SQLStore( $this->newStoreConfig() );
		return $sqlStore->newInstaller( $this->newTableBuilder() );
	}

	public function newDumpStoreInstaller() {
		return new StoreInstaller( $this->newTableBuilder() );
	}

	private function newStoreConfig() {
		$config = new StoreConfig(
			'QueryR Replicator QueryEngine',
			'qr_',
			array(
				'number' => new NumberHandler()
			)
		);

		$config->setPropertyDataValueTypeLookup( new StubPropertyDataValueTypeLookup() );

		return $config;
	}

	private function newTableBuilder() {
		$pdoFactory = new PDOFactory( $this->pdo );
		return $pdoFactory->newMySQLTableBuilder( $this->dbName );
	}

}
