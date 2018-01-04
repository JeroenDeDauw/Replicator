<?php

namespace Tests\Queryr\Replicator\Integration\Importer;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\Cli\Command\GzJsonImportCommand;
use Queryr\Replicator\Plugin\EntityHandlerPlugin;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Queryr\Replicator\Integration\TestEnvironment;
use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LameLoggerPluginTest extends TestCase {

	public function setUp() {
		$this->markTestSkipped( 'Plugin system incomplete' ); // TODO

		global $replicatorEntityHandlers;

		$replicatorEntityHandlers[] = function() {
			return [
				'input-options' => [],
				'is-enabled-function' => function() {
					return true;
				},
				'handler-builder-function' => function() {
					return new class() implements EntityHandlerPlugin {
						private $filePath;

						public function __construct() {
							$this->filePath = '/tmp/LameLogging.txt';
						}

						public function handleEntity( EntityDocument $entity ) {
							file_put_contents(
								$this->filePath,
								$entity->getId()->getSerialization() . "\n",
								FILE_APPEND
							);
						}

						public function getHandlingMessage( EntityDocument $entity ): string {
							return 'Doing some lame logging';
						}
					};
				}
			];
		};
	}

	public function testEntityIdInOutput() {
		$output = $this->getOutputForArgs( [
			'file' => 'tests/data/simple/five-entities.json.gz'
		] );

		$this->assertContains( 'Q1', $output );
		$this->assertContains( 'Q8', $output );
		$this->assertContains( 'P16', $output );
		$this->assertContains( 'P19', $output );
		$this->assertContains( 'P22', $output );

		$this->assertContains( 'Doing some lame logging', $output );

		$this->assertContains( 'Entity imported', $output );
	}

	private function getOutputForArgs( array $args ) {
		$commandTester = $this->newCommandTester();

		$commandTester->execute( $args );

		return $commandTester->getDisplay();
	}

	private function newCommandTester() {
		$command = new GzJsonImportCommand();
		$command->setServiceFactory( TestEnvironment::newInstance()->getFactory() );

		return new CommandTester( $command );
	}

}
