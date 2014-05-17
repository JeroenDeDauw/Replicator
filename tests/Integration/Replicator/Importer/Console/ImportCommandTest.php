<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use Doctrine\DBAL\DriverManager;
use Queryr\Replicator\Importer\Console\ImportCommand;
use Queryr\Replicator\ServiceFactory;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Queryr\Replicator\Importer\Console\ImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportCommandTest extends \PHPUnit_Framework_TestCase {

	public function testEntityIdInOutput() {
		$this->assertRegExp(
			'/Q15831780/',
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
		$command = new ImportCommand();
		$command->setServiceFactory( ServiceFactory::newFromConnection( $this->newConnection() ) );

		return new CommandTester( $command );
	}

	private function newConnection() {
		return DriverManager::getConnection( array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		) );
	}

	public function testResume() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-items.xml',
			'--continue' => 'Q15826086'
		] );

		$this->assertNotRegExp( '/Q15831779/', $output );
		$this->assertNotRegExp( '/Q15831780/', $output );
		$this->assertRegExp( '/Q15826087/', $output );
		$this->assertRegExp( '/Q15826088/', $output );
		$this->assertRegExp( '/2 entities/', $output );
	}

}