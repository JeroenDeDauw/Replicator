<?php

namespace Tests;

use Ask\Language\Description\SomeProperty;
use Ask\Language\Description\ValueDescription;
use Ask\Language\Option\QueryOptions;
use DataValues\NumberValue;
use Tests\Fixtures\TestFixtureFactory;
use Wikibase\Database\PDO\PDOFactory;
use Wikibase\Database\Schema\TableDeletionFailedException;
use Wikibase\DataModel\Claim\Statement;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;
use Wikibase\QueryEngine\QueryEngine;
use Wikibase\QueryEngine\QueryEngineException;
use Wikibase\QueryEngine\QueryStoreWriter;
use Wikibase\QueryEngine\SQLStore\DVHandler\NumberHandler;
use Wikibase\QueryEngine\SQLStore\SQLStore;
use Wikibase\QueryEngine\SQLStore\StoreConfig;

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
		$sqlStore = new SQLStore( $this->newStoreConfig() );

		$factory = new PDOFactory( $this->newPDO() );
		$tableBuilder = $factory->newMySQLTableBuilder( 'replicator_tests' );
		$queryInterface = $factory->newMySQLQueryInterface();

		try {
			$sqlStore->newUninstaller( $tableBuilder )->uninstall();
		}
		catch ( QueryEngineException $ex ) {}

		$sqlStore->newInstaller( $tableBuilder )->install();

		$this->writer = $sqlStore->newWriter( $queryInterface );
		$this->queryEngine = $sqlStore->newQueryEngine( $queryInterface );
	}

	private function newPDO() {
		try {
			return $this->pdo = TestFixtureFactory::newInstance()->newPDO();
		}
		catch ( \PDOException $ex ) {
			$this->markTestSkipped( 'Test not run, presumably the database is not set up: ' . $ex->getMessage() );
		}
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

	public function testInsertItem() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'Q100' ) );

		$claim = new Statement( new PropertyValueSnak( 42, new NumberValue( 1337 ) ) );
		$claim->setGuid( 'foo claim' );
		$item->addClaim( $claim );

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