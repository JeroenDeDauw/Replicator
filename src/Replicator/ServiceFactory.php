<?php

namespace QueryR\Replicator;

use DataValues\Deserializers\DataValueDeserializer;
use PDO;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\Dump\Store\Store;
use Wikibase\Dump\Store\StoreInstaller;
use Wikibase\InternalSerialization\DeserializerFactory;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
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

	/**
	 * @var PDOFactory
	 */
	private $pdoFactory;

	private $dbName;

	private function __construct( PDO $pdo, $dbName ) {
		$this->pdoFactory = new PDOFactory( $pdo );
		$this->dbName = $dbName;
	}

	public static function newFromConfig() {
		// TODO: read from file
		$user = 'replicator';
		$password = 'queryisawesome';
		$dbName = 'replicator';

		// TODO: exception handling
		$pdo = new PDO(
			"mysql:dbname=$dbName;host=localhost;",
			$user,
			$password
		);

		return new self( $pdo, $dbName );
	}

	public function newQueryEngineInstaller() {
		$sqlStore = $this->newSqlStore();
		return $sqlStore->newInstaller( $this->newTableBuilder() );
	}

	private function newSqlStore() {
		 return new SQLStore( $this->newStoreConfig() );
	}

	public function newDumpStoreInstaller() {
		return new StoreInstaller( $this->newTableBuilder() );
	}

	private function newStoreConfig() {
		$handlers = new DataValueHandlers();

		$h = $handlers->getHandlers();

		$config = new StoreConfig(
			'QueryR Replicator QueryEngine',
			'qr_',
			$h
		);

		$config->setPropertyDataValueTypeLookup( new StubPropertyDataValueTypeLookup() );

		return $config;
	}

	private function newTableBuilder() {
		return $this->pdoFactory->newMySQLTableBuilder( $this->dbName );
	}

	public function newDumpStore() {
		return new Store( $this->newQueryInterface() );
	}

	private function newQueryInterface() {
		return $this->pdoFactory->newMySQLQueryInterface();
	}

	public function newEntityDeserializer() {
		$dataValueClasses = array_merge(
			$GLOBALS['evilDataValueMap'],
			array(
				'globecoordinate' => 'DataValues\GlobeCoordinateValue',
				'monolingualtext' => 'DataValues\MonolingualTextValue',
				'multilingualtext' => 'DataValues\MultilingualTextValue',
				'quantity' => 'DataValues\QuantityValue',
				'time' => 'DataValues\TimeValue',
				'wikibase-entityid' => 'Wikibase\DataModel\Entity\EntityIdValue',
			)
		);

		$factory = new DeserializerFactory(
			new DataValueDeserializer( $dataValueClasses ),
			new BasicEntityIdParser()
		);

		return $factory->newEntityDeserializer();
	}

	public function newQueryStoreWriter() {
		return $this->newSqlStore()->newWriter( $this->newQueryInterface() );
	}

}
