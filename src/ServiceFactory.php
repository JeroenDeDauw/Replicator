<?php

namespace Queryr\Replicator;

use DataValues\Deserializers\DataValueDeserializer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Queryr\EntityStore\EntityStore;
use Queryr\EntityStore\EntityStoreConfig;
use Queryr\EntityStore\EntityStoreFactory;
use Queryr\EntityStore\EntityStoreInstaller;
use Queryr\TermStore\TermStore;
use Queryr\TermStore\TermStoreConfig;
use Queryr\TermStore\TermStoreInstaller;
use RuntimeException;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\InternalSerialization\DeserializerFactory;
use Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ServiceFactory {

	const QUERY_ENGINE_PREFIX = 'qe_';
	const ENTITY_STORE_PREFIX = 'es_';
	const TERMS_STORE_PREFIX = 'ts_';

	public static function newFromConnection( Connection $connection ) {
		return new self( $connection );
	}

	/**
	 * @return self
	 * @throws RuntimeException
	 */
	public static function newFromConfig() {
		$config = ConfigFile::newInstance()->read();

		try {
			$connection = DriverManager::getConnection( $config );
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

	public function newQueryEngineUninstaller() {
		$sqlStore = $this->newSqlStore();
		return $sqlStore->newUninstaller( $this->connection->getSchemaManager() );
	}

	private function newSqlStore() {
		$handlers = new DataValueHandlersBuilder();

		$schema = new StoreSchema(
			self::QUERY_ENGINE_PREFIX,
			$handlers->withSimpleMainSnakHandlers()->getHandlers()
		);

		$config = new StoreConfig( 'QueryR Replicator QueryEngine' );

		return new SQLStore( $schema, $config );
	}

	public function newEntityStoreInstaller() {
		return new EntityStoreInstaller(
			$this->connection->getSchemaManager(),
			new EntityStoreConfig( self::ENTITY_STORE_PREFIX )
		);
	}

	public function newEntityStore() {
		return $this->newEntityStoreFactory()->newEntityStore();
	}

	public function newItemStore() {
		return $this->newEntityStoreFactory()->newItemStore();
	}

	private function newEntityStoreFactory() {
		return new EntityStoreFactory(
			$this->connection,
			new EntityStoreConfig( self::ENTITY_STORE_PREFIX )
		);
	}

	public function newTermStore() {
		return new TermStore(
			$this->connection,
			new TermStoreConfig( self::TERMS_STORE_PREFIX )
		);
	}

	private function newDataValueDeserializer() {
		$dataValueClasses = [
			'boolean' => 'DataValues\BooleanValue',
			'number' => 'DataValues\NumberValue',
			'string' => 'DataValues\StringValue',
			'unknown' => 'DataValues\UnknownValue',
			'globecoordinate' => 'DataValues\GlobeCoordinateValue', // Geo\Values\
			'monolingualtext' => 'DataValues\MonolingualTextValue',
			'multilingualtext' => 'DataValues\MultilingualTextValue',
			'quantity' => 'DataValues\QuantityValue',
			'time' => 'DataValues\TimeValue',
			'wikibase-entityid' => 'Wikibase\DataModel\Entity\EntityIdValue',
		];

		return new DataValueDeserializer( $dataValueClasses );
	}

	private function newEntityIdParser() {
		return new BasicEntityIdParser();
	}

	public function newLegacyEntityDeserializer() {
		$factory = new DeserializerFactory(
			$this->newDataValueDeserializer(),
			$this->newEntityIdParser()
		);

		return $factory->newEntityDeserializer();
	}

	public function newCurrentEntityDeserializer() {
		$factory = new \Wikibase\DataModel\DeserializerFactory(
			$this->newDataValueDeserializer(),
			$this->newEntityIdParser()
		);

		return $factory->newEntityDeserializer();
	}

	public function newQueryStoreWriter() {
		return $this->newSqlStore()->newWriter( $this->connection );
	}

	public function newTermStoreInstaller() {
		return new TermStoreInstaller(
			$this->connection->getSchemaManager(),
			new TermStoreConfig( self::TERMS_STORE_PREFIX )
		);
	}

}
