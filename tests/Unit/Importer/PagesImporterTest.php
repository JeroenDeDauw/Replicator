<?php

namespace Tests\Queryr\Replicator\Importer;

use Queryr\Replicator\Importer\PagesImporter;
use Queryr\Replicator\Model\EntityPage;

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

		$importer->importPages( new \ArrayIterator( [
			new EntityPage( 'first', 'first', 1, 100, 'foo' ),
			new EntityPage( 'second', 'second', 2, 200, 'foo' ),
			new EntityPage( 'third', 'third', 3, 300, 'foo' )
		] ) );
	}

}

