<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use Doctrine\DBAL\DriverManager;
use Queryr\Replicator\Importer\Console\DumpImportCommand;
use Queryr\Replicator\ServiceFactory;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Queryr\Replicator\Importer\Console\DumpImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpImportCommandTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var ServiceFactory
	 */
	private $factory;

	public function setUp() {
		$this->factory = ServiceFactory::newFromConnection( $this->newConnection() );

		$this->factory->newDumpStoreInstaller()->install();
		$this->factory->newQueryEngineInstaller()->install();
	}

	private function newConnection() {
		return DriverManager::getConnection( array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		) );
	}

	public function testEntityIdInOutput() {
		$this->assertContains(
			'Q15831780',
			$this->getOutputForArgs( [
				'file' => 'tests/data/simple/one-item.xml'
			] )
		);
	}

	private function getOutputForArgs( array $args ) {
		$commandTester = $this->newCommandTester();

		$commandTester->execute( $args );

		return $commandTester->getDisplay();
	}

	private function newCommandTester() {
		$command = new DumpImportCommand();
		$command->setServiceFactory( $this->factory );

		return new CommandTester( $command );
	}

	public function testResume() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-items.xml',
			'--continue' => 'Q15826086'
		] );

		$this->assertNotContains( 'Q15831779', $output );
		$this->assertNotContains( 'Q15831780', $output );
		$this->assertContains( 'Q15826087', $output );
		$this->assertContains( 'Q15826088', $output );
		$this->assertContains( 'Entities: 2', $output );
	}

}