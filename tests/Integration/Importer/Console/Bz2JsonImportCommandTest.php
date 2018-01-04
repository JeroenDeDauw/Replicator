<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\Cli\Command\Bz2JsonImportCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @covers \Queryr\Replicator\Cli\Command\Bz2JsonImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Bz2JsonImportCommandTest extends TestCase {

	public function setUp() {
		if ( !function_exists( 'bzopen' ) ) {
			$this->markTestSkipped( 'bzip2 library not installed' );
		}
	}

	public function testEntityIdInOutput() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-entities.json.bz2'
		] );

		$this->assertContains( 'Q1', $output );
		$this->assertContains( 'Q8', $output );
		$this->assertContains( 'P16', $output );
		$this->assertContains( 'P19', $output );
		$this->assertContains( 'P22', $output );

		$this->assertContains( 'Entity imported', $output );
	}

	private function getOutputForArgs( array $args ) {
		$commandTester = $this->newCommandTester();

		$commandTester->execute( $args );

		return $commandTester->getDisplay();
	}

	private function newCommandTester() {
		$command = new Bz2JsonImportCommand();
		$command->setServiceFactory( TestEnvironment::newInstance()->getFactory() );

		return new CommandTester( $command );
	}

	public function testMaxArgumentIsRespected() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-entities.json.bz2',
			'--max' => '3'
		] );

		$this->assertContains( 'Q1', $output );
		$this->assertContains( 'Q8', $output );
		$this->assertContains( 'P16', $output );
		$this->assertNotContains( 'P19', $output );
		$this->assertNotContains( 'P22', $output );
	}

}