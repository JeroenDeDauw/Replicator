<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\Cli\Command\JsonDumpImportCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @covers \Queryr\Replicator\Cli\Command\JsonDumpImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonDumpImportCommandTest extends TestCase {

	public function testEntityIdInOutput() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-entities.json'
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
		$command = new JsonDumpImportCommand();
		$command->setServiceFactory( TestEnvironment::newInstance()->getFactory() );

		return new CommandTester( $command );
	}

	public function testMaxArgumentIsRespected() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-entities.json',
			'--max' => '3'
		] );

		$this->assertContains( 'Q1', $output );
		$this->assertContains( 'Q8', $output );
		$this->assertContains( 'P16', $output );
		$this->assertNotContains( 'P19', $output );
		$this->assertNotContains( 'P22', $output );

		$this->assertContains( '--continue', $output );
	}

	public function testContinuation() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-entities.json',
			'--continue' => '66943'
		] );

		$this->assertNotContains( 'Q1', $output );
		$this->assertNotContains( 'Q8', $output );
		$this->assertNotContains( 'P16', $output );
		$this->assertContains( 'P19', $output );
		$this->assertContains( 'P22', $output );
	}

}