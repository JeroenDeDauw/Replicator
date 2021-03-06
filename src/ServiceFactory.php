<?php

namespace Queryr\Replicator;

use DataValues\BooleanValue;
use DataValues\Deserializers\DataValueDeserializer;
use DataValues\Geo\Values\GlobeCoordinateValue;
use DataValues\MonolingualTextValue;
use DataValues\MultilingualTextValue;
use DataValues\NumberValue;
use DataValues\QuantityValue;
use DataValues\Serializers\DataValueSerializer;
use DataValues\StringValue;
use DataValues\TimeValue;
use DataValues\UnknownValue;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Queryr\EntityStore\EntityStoreConfig;
use Queryr\EntityStore\EntityStoreFactory;
use Queryr\EntityStore\EntityStoreInstaller;
use Queryr\Replicator\Importer\CompositeReporter;
use Queryr\Replicator\Importer\EntityHandlers\EntityStoreEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\QueryEngineEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\TermStoreEntityHandler;
use Queryr\Replicator\Importer\LoggingReporter;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\PagesImporter;
use Queryr\Replicator\Importer\StatsReporter;
use Queryr\Replicator\Model\EntityPage;
use Queryr\TermStore\TermStoreConfig;
use Queryr\TermStore\TermStoreInstaller;
use Queryr\TermStore\TermStoreWriter;
use RuntimeException;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\SerializerFactory;
use Wikibase\InternalSerialization\DeserializerFactory;
use Wikibase\JsonDumpReader\DumpReader;
use Wikibase\JsonDumpReader\JsonDumpFactory;
use Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 *
 * @SuppressWarnings(PHPMD)
 */
class ServiceFactory {

	const QUERY_ENGINE_PREFIX = 'qe_';
	const ENTITY_STORE_PREFIX = 'es_';
	const TERMS_STORE_PREFIX = 'ts_';

	public static function newFromConnection( Connection $connection ) {
		return new self( $connection );
	}

	/**
	 * @throws RuntimeException
	 */
	public static function newFromConfig(): self {
		$config = DatabaseConfigFile::newInstance()->read();

		try {
			$connection = DriverManager::getConnection( $config );
		}
		catch ( DBALException $ex ) {
			throw new RuntimeException(
				'Could not establish database connection: ' . $ex->getMessage()
			);
		}

		$factory = new self( $connection );
		$factory->setLogger( $factory->newProductionLogger() );

		return $factory;
	}

	/**
	 * @var Connection
	 */
	private $connection;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	private function __construct( Connection $connection ) {
		$this->connection = $connection;
		$this->logger = new NullLogger();
	}

	public function newProductionLogger() {
		$logger = new Logger( 'Replicator logger' );

		$streamHandler = new StreamHandler( $this->newLoggerPath( ( new \DateTime() )->format( 'Y-m-d\TH:i:s\Z' ) ) );
		$bufferHandler = new BufferHandler( $streamHandler, 500, Logger::INFO, true, true );
		$streamHandler->setFormatter( new LineFormatter( "%message%\n" ) );
		$logger->pushHandler( $bufferHandler );

		$errorHandler = new StreamHandler( $this->newLoggerPath( 'error' ), Logger::ERROR );
		$errorHandler->setFormatter( new JsonFormatter() );
		$logger->pushHandler( $errorHandler );

		return $logger;
	}

	private function newLoggerPath( $fileName ) {
		return __DIR__ . '/../var/log/' . $fileName . '.log';
	}

	public function setLogger( LoggerInterface $logger ) {
		$this->logger = $logger;
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

	public function newPagesImporter( PageImportReporter $reporter,
		StatsReporter $statsReporter, callable $onAborted = null ) {

		return new PagesImporter(
			$this->newPageImporter( $reporter ),
			$statsReporter,
			$onAborted
		);
	}

	public function newJsonEntityPageIterator( DumpReader $reader ) {
		$entityPageIterator = function( \Iterator $dumpIterator ) {
			foreach ( $dumpIterator as $entity ) {
				yield new EntityPage(
					json_encode( $entity ),
					$entity['type'] === 'property' ? 'Property:' . $entity['id'] : $entity['id'],
					0,
					0,
					( new \DateTime() )->format( 'Y-m-d\TH:i:s\Z' )
				);
			}
		};

		$dumpIterator = ( new JsonDumpFactory() )->newObjectDumpIterator( $reader );
		return $entityPageIterator( $dumpIterator );
	}

	public function newPageImporter( PageImportReporter $reporter ) {
		$loggingReporter = new LoggingReporter( $this->logger );
		$compositeReporter = new CompositeReporter( $loggingReporter, $reporter );

		return new PageImporter(
			$this->newLegacyEntityDeserializer(),
			$this->getEntityHandlers(),
			[
				new EntityStoreEntityHandler( $this->newEntityStore() )
			],
			$compositeReporter
		);
	}

	private function getEntityHandlers() {
		$handlers = [
			new TermStoreEntityHandler( $this->newTermStoreWriter() )
		];

		if ( defined( 'WIKIBASE_QUERYENGINE_VERSION' ) ) {
			$handlers[] = new QueryEngineEntityHandler( $this->newQueryStoreWriter() );
		}

		return $handlers;
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

	public function newTermStoreWriter() {
		return new TermStoreWriter(
			$this->connection,
			new TermStoreConfig( self::TERMS_STORE_PREFIX )
		);
	}

	private function newDataValueDeserializer() {
		return new DataValueDeserializer( [
			'boolean' => BooleanValue::class,
			'number' => NumberValue::class,
			'string' => StringValue::class,
			'unknown' => UnknownValue::class,
			'globecoordinate' => GlobeCoordinateValue::class,
			'monolingualtext' => MonolingualTextValue::class,
			'multilingualtext' => MultilingualTextValue::class,
			'quantity' => QuantityValue::class,
			'time' => TimeValue::class,
			'wikibase-entityid' => EntityIdValue::class,
		] );
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

	public function newCurrentEntitySerializer() {
		$factory = new SerializerFactory(
			new DataValueSerializer()
		);

		return $factory->newEntitySerializer();
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
