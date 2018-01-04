<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\Cli\Command\ApiImportCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Queryr\Replicator\Fixtures\FakeHttp;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @covers \Queryr\Replicator\Cli\Command\ApiImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiImportCommandTest extends TestCase {

	public function testEntityIdInOutput() {
		$output = $this->getOutputForArgs( [
			'entities' => [ 'Q1' ]
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
		$command = new ApiImportCommand();
		$command->setServiceFactory( TestEnvironment::newInstance()->getFactory() );
		$command->setHttp( new FakeHttp() );

		return new CommandTester( $command );
	}

}

