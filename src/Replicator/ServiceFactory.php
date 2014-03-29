<?php

namespace QueryR\Replicator;

use PDO;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\Dump\Store\StoreInstaller;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ServiceFactory {

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

	public function newQueryEngineInstaller( PDO $pdo, $dbName ) {
		$sqlStore = new SQLStore( $this->newStoreConfig() );
		return $sqlStore->newInstaller( $this->newTableBuilderFromArgs( $pdo, $dbName ) );
	}

	private function newTableBuilderFromArgs( PDO $pdo, $dbName ) {
		$pdoFactory = new PDOFactory( $pdo );
		return $pdoFactory->newMySQLTableBuilder( $dbName );
	}

	public function newDumpStoreInstaller( PDO $pdo, $dbName ) {
		return new StoreInstaller( $this->newTableBuilderFromArgs( $pdo, $dbName ) );
	}

}

class StubPropertyDataValueTypeLookup implements PropertyDataValueTypeLookup {

	public function getDataValueTypeForProperty( PropertyId $propertyId ) {
		return 'number';
	}

}