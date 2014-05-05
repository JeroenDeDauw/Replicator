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
		$command = new ImportCommand();
		$command->setServiceFactory( ServiceFactory::newFromConnection( $this->newConnection() ) );

		$commandTester = new CommandTester( $command );

		$commandTester->execute( array(
			'file' => 'tests/data/simple/one-item.xml'
		) );

		$this->assertRegExp( '/Q15831780/', $commandTester->getDisplay() );
	}

	private function newConnection() {
		return DriverManager::getConnection( array(
			'driver' => 'pdo_sqlite',
			'memory' => true,
		) );
	}

}