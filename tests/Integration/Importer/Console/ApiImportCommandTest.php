<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use Queryr\Replicator\Cli\Command\ApiImportCommand;
use Queryr\Replicator\EntitySource\Api\Http;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @covers Queryr\Replicator\Cli\Command\ApiImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiImportCommandTest extends \PHPUnit_Framework_TestCase {

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

class FakeHttp extends Http {

	public function get( $url ) {
		if ( $url === 'https://www.wikidata.org/w/api.php?action=wbgetentities&ids=Q1&format=json' ) {
			return file_get_contents( __DIR__ . '/../../../data/api/Q1.json' );
		}
		else {
			throw new \Exception();
		}
	}

}