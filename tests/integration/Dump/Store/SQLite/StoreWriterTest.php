<?php

namespace Tests\Wikibase\Dump\Store;

use PDO;
use Wikibase\Database\MySQL\MySQLConditionSqlBuilder;
use Wikibase\Database\MySQL\MySQLDeleteSqlBuilder;
use Wikibase\Database\MySQL\MySQLInsertSqlBuilder;
use Wikibase\Database\MySQL\MySQLSelectSqlBuilder;
use Wikibase\Database\MySQL\MySQLUpdateSqlBuilder;
use Wikibase\Database\PDO\PDOQueryInterface;
use Wikibase\Database\PDO\PDOTableBuilder;
use Wikibase\Database\QueryInterface\QueryInterface;
use Wikibase\Database\SQLite\SQLiteFieldSqlBuilder;
use Wikibase\Database\SQLite\SQLiteIndexSqlBuilder;
use Wikibase\Database\SQLite\SQLiteTableSqlBuilder;
use Wikibase\Dump\Page;
use Wikibase\Dump\Revision;
use Wikibase\Dump\Store\DumpStore;
use Wikibase\Dump\Store\SQLite\SQLiteDumpStore;
use Wikibase\Dump\Store\SQLite\StoreInstaller;
use Wikibase\Dump\Store\SQLite\StoreWriter;

/**
 * @covers Wikibase\Dump\Store\SQLite\StoreWriter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreWriterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var DumpStore
	 */
	private $store;

	/**
	 * @var PDO
	 */
	private $pdo;

	/**
	 * @var QueryInterface
	 */
	private $queryInterface;

	public function setUp() {
		$this->pdo = new PDO(
			'mysql:dbname=replicator_tests;host=localhost',
			'replicator',
			'mysql_is_evil'
		);

		$this->queryInterface = $this->newQueryInterface();

		$tableBuilder = $this->newTableBuilder();

		$this->store = new SQLiteDumpStore(
			new StoreInstaller( $tableBuilder ),
			new StoreWriter( $this->queryInterface )
		);

		if ( $tableBuilder->tableExists( 'entities' ) ) {
			$this->store->uninstall();
		}

		$this->store->install();
	}

	private function newTableBuilder() {
		$escaper = new PDOEscaper( $this->pdo );
		$tableNameFormatter = $this->newTableNameFormatter();

		return new PDOTableBuilder(
			$this->pdo,
			new SQLiteTableSqlBuilder(
				$escaper,
				$tableNameFormatter,
				new SQLiteFieldSqlBuilder( $escaper ),
				new SQLiteIndexSqlBuilder( $escaper, $tableNameFormatter )
			)
		);
	}

	private function newTableNameFormatter() {
		return new PrefixingTableNameFormatter( '' );
	}

	private function newQueryInterface() {
		$escaper = new PDOEscaper( $this->pdo );
		$tableNameFormatter = $this->newTableNameFormatter();

		$conditionBuilder = new MySQLConditionSqlBuilder( $escaper, $escaper );

		return new PDOQueryInterface(
			$this->pdo,
			new MySQLInsertSqlBuilder( $escaper, $tableNameFormatter ),
			new MySQLUpdateSqlBuilder( $escaper, $tableNameFormatter, $conditionBuilder ),
			new MySQLDeleteSqlBuilder( $escaper, $conditionBuilder ),
			new MySQLSelectSqlBuilder( $escaper, $conditionBuilder )
		);
	}

	public function testStoresPage() {
		$this->store->storePage( new Page(
			'1337',
			'Q1337',
			'42',
			new Revision(
				'9001',
				'wikidata-item',
				'application/json',
				'foo bar baz bah',
				'2014-02-27T11:40:12Z'
			)
		) );

		$resultIterator = $this->queryInterface->select(
			'entities',
			array(
				'page_id',
				'page_title',
				'page_namespace',

				'revision_id',
				'revision_model',
				'revision_format',
				'revision_time',

				'entity',
			),
			array(
				'page_id' => '1337'
			)
		);

		$resultArray = iterator_to_array( $resultIterator );

		$this->assertCount( 1, $resultArray );
		$resultRow = (object)array_shift( $resultArray );

		$this->assertEquals( 1337, $resultRow->page_id );
		$this->assertEquals( 'Q1337', $resultRow->page_title );
		$this->assertEquals( 42, $resultRow->page_namespace );

		$this->assertEquals( 9001, $resultRow->revision_id );
		$this->assertEquals( 'wikidata-item', $resultRow->revision_model );
		$this->assertEquals( 'application/json', $resultRow->revision_format );
		$this->assertEquals( '2014-02-27T11:40:12Z', $resultRow->revision_time );

		$this->assertEquals( 'foo bar baz bah', $resultRow->entity );
	}

}
