<?php

namespace Tests\Queryr\Replicator\Integration\Importer;

use Tests\Queryr\Replicator\Integration\TestEnvironment;
use Wikibase\JsonDumpReader\JsonDumpReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageImportingTest extends \PHPUnit_Framework_TestCase {

	public function testFoo() {
		$factory = TestEnvironment::newInstance()->getFactory();

		$importer = $factory->newPagesImporter(
			$this->getMock( 'Queryr\Replicator\Importer\PageImportReporter' ),
			$this->getMock( 'Queryr\Replicator\Importer\StatsReporter' )
		);

		$dumpReader = new JsonDumpReader( ( new \JsonDumpData() )->getFiveEntitiesDumpPath() );

		$importer->importPages( $factory->newJsonEntityPageIterator( $dumpReader ) );

		$this->assertTrue( true );

		// TODO: test with error occuring in QE, next entities sitll need to be inserted
	}

}
