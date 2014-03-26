<?php

namespace Tests\Wikibase\Dump\Store;

use PDO;
use Wikibase\Database\NullTableNameFormatter;
use Wikibase\Database\PDO\PDOEscaper;
use Wikibase\Database\PDO\PDOTableBuilder;
use Wikibase\Database\Schema\TableBuilder;
use Wikibase\Database\SQLite\SQLiteFieldSqlBuilder;
use Wikibase\Database\SQLite\SQLiteIndexSqlBuilder;
use Wikibase\Database\SQLite\SQLiteTableSqlBuilder;
use Wikibase\Dump\Store\SQLite\StoreInstaller;

/**
 * @covers Wikibase\Dump\Store\SQLite\StoreInstaller
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class StoreInstallerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var StoreInstaller
	 */
	private $store;

	/**
	 * @var TableBuilder
	 */
	private $tableBuilder;

	public function setUp() {
		$pdo = new PDO( 'sqlite::memory:' );
		$escaper = new PDOEscaper( $pdo );
		$tableNameFormatter = new NullTableNameFormatter();

		$this->tableBuilder = new PDOTableBuilder(
			$pdo,
			new SQLiteTableSqlBuilder(
				$escaper,
				$tableNameFormatter,
				new SQLiteFieldSqlBuilder( $escaper ),
				new SQLiteIndexSqlBuilder( $escaper, $tableNameFormatter )
			),
			$tableNameFormatter,
			$escaper
		);

		$this->store = new StoreInstaller( $this->tableBuilder );
	}

	public function testInstallationAndRemoval() {
		$this->store->install();

		$this->assertTrue( $this->tableBuilder->tableExists( 'entities' ) );

		$this->store->uninstall();

		$this->assertFalse( $this->tableBuilder->tableExists( 'entities' ) );
	}

	public function testStoresPage() {
		$this->store->install();
	}

}
