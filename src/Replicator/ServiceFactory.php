<?php

namespace Queryr\Replicator;

use DataValues\Deserializers\DataValueDeserializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use PDO;
use Queryr\Dump\Store\Store;
use Queryr\Dump\Store\StoreInstaller;
use RuntimeException;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\InternalSerialization\DeserializerFactory;
use Wikibase\QueryEngine\SQLStore\DataValueHandlers;
use Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ServiceFactory {

	public static function newForInstaller( Connection $connection ) {
		return new self( $connection );
	}

	/**
	 * @return ServiceFactory
	 * @throws RuntimeException
	 */
	public static function newFromConfig() {
		$config = ConfigFile::newInstance()->read();

		try {
			$connection = DriverManager::getConnection( array(
				'driver' => 'pdo_mysql',
				'user' => $config['user'],
				'password' => $config['password'],
				'host' => 'localhost',
				'dbname' => $config['database'],
				'port' => '3306'
			) );
		}
		catch ( DBALException $ex ) {
			throw new RuntimeException(
				'Could not establish database connection: ' . $ex->getMessage()
			);
		}

		return new self( $connection );
	}

	private $connection;

	private function __construct( Connection $connection ) {
		$this->connection = $connection;
	}

	public function newQueryEngineInstaller() {
		$sqlStore = $this->newSqlStore();
		return $sqlStore->newInstaller( $this->connection->getSchemaManager() );
	}

	private function newSqlStore() {
		$handlers = new DataValueHandlersBuilder();

		$schema = new StoreSchema(
			'qr_',
			$handlers->withSimpleHandlers()
				->withEntityIdHandler( new BasicEntityIdParser() )
				->getHandlers()
		);

		$config = new StoreConfig( 'QueryR Replicator QueryEngine' );

		return new SQLStore( $schema, $config );
	}

	public function newDumpStoreInstaller() {
		return new StoreInstaller( $this->connection->getSchemaManager() );
	}

	public function newDumpStore() {
		return new Store( $this->connection );
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
		return $this->newSqlStore()->newWriter( $this->connection );
	}

}
