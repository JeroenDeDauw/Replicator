<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use Queryr\Replicator\Cli\Command\JsonDumpImportCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @covers Queryr\Replicator\Cli\Command\JsonDumpImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonDumpImportCommandTest extends \PHPUnit_Framework_TestCase {

	public function testEntityIdInOutput() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/one-item.json'
		] );

		$this->assertContains( 'Q1', $output );
		$this->assertContains( 'Entity imported', $output );
	}

	private function getOutputForArgs( array $args ) {
		$commandTester = $this->newCommandTester();

		$commandTester->execute( $args );

		return $commandTester->getDisplay();
	}

	private function newCommandTester() {
		$command = new JsonDumpImportCommand();
		$command->setServiceFactory( TestEnvironment::newInstance()->getFactory() );

		return new CommandTester( $command );
	}

}