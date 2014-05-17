<?php

namespace Tests\Queryr\Replicator\Importer;

use Queryr\Dump\Reader\Page;
use Queryr\Dump\Reader\Revision;
use Queryr\Replicator\Importer\PagesImporter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PagesImporterTest extends \PHPUnit_Framework_TestCase {

	public function testCallsImportForEachPage() {
		$pageImporter = $this->getMockBuilder( 'Queryr\Replicator\Importer\PageImporter' )
			->disableOriginalConstructor()->getMock();

		$pageImporter->expects( $this->exactly( 3 ) )
			->method( 'import' );

		$pageImporter->expects( $this->any() )
			->method( 'getReporter' )
			->will( $this->returnValue( $this->getMock( 'Queryr\Replicator\Importer\PageImportReporter' ) ) );

		$statsReporter = $this->getMock( 'Queryr\Replicator\Importer\StatsReporter' );

		$importer = new PagesImporter( $pageImporter, $statsReporter );

		$importer->importPages( new \ArrayIterator( array(
			new Page( 1, 'first', 1, new Revision( 1, 'foo', 'bar', 'baz', 'bah' ) ),
			new Page( 2, 'second', 1, new Revision( 1, 'foo', 'bar', 'baz', 'bah' ) ),
			new Page( 3, 'third', 1, new Revision( 1, 'foo', 'bar', 'baz', 'bah' ) )
		) ) );
	}

}

