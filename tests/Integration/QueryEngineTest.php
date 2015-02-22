<?php

namespace Tests;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use Doctrine\DBAL\DriverManager;
use Wikibase\DataModel\Claim\Claim;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\QueryEngine;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\DataValueHandlersBuilder;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;
use Wikibase\QueryEngine\SQLStore\StoreSchema;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class QueryEngineTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var QueryStoreWriter
	 */
	private $writer;

	/**
	 * @var QueryEngine
	 */
	private $queryEngine;

	public function setUp() {
		$sqlStore = new SQLStore( $this->newStoreSchema(), $this->newStoreConfig() );

		$connection = $this->newConnection();

		$sqlStore->newInstaller( $connection->getSchemaManager() )->install();

		$this->writer = $sqlStore->newWriter( $connection );

		$this->queryEngine = $sqlStore->newQueryEngine(
			$connection,
			new StubPropertyDataValueTypeLookup(),
			new BasicEntityIdParser()
		);
	}

	private function newConnection() {
		return DriverManager::getConnection( array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		) );
	}

	private function newStoreSchema() {
		$handlersBuilder = new DataValueHandlersBuilder();

		return new StoreSchema(
			'qe_',
			$handlersBuilder->withSimpleMainSnakHandlers()->getHandlers()
		);
	}

	private function newStoreConfig() {
		$config = new StoreConfig(
			'QueryR Replicator QueryEngine'
		);

		return $config;
	}

	public function testInsertItem() {
		$item = new Item();
		$item->setId( new ItemId( 'Q100' ) );

		$statement = new Statement( new Claim( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) ) );
		$statement->setGuid( 'foo claim' );
		$item->getStatements()->addStatement( $statement );

		$this->writer->insertEntity( $item );

		$propertyDescription = new SomeProperty(
			new EntityIdValue( new PropertyId( 'P42' ) ),
			new ValueDescription( new NumberValue( 1337 ) )
		);

		$ids = $this->queryEngine->getMatchingEntities(
			$propertyDescription,
			new QueryOptions( 2, 0 )
		);

		$this->assertEquals( array( 'Q100' ), $ids );
	}

}

class StubPropertyDataValueTypeLookup implements PropertyDataValueTypeLookup {

	public function getDataValueTypeForProperty( PropertyId $propertyId ) {
		return 'number';
	}

}