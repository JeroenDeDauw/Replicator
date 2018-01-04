<?php

namespace Tests\Queryr\Replicator\Importer;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\PagesImporter;
use Queryr\Replicator\Importer\StatsReporter;
use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PagesImporterTest extends TestCase {

	public function testCallsImportForEachPage() {
		$pageImporter = $this->createMock( PageImporter::class );

		$pageImporter->expects( $this->exactly( 3 ) )
			->method( 'import' );

		$pageImporter->expects( $this->any() )
			->method( 'getReporter' )
			->will( $this->returnValue( $this->createMock( PageImportReporter::class ) ) );

		$statsReporter = $this->createMock( StatsReporter::class );

		$importer = new PagesImporter( $pageImporter, $statsReporter );

		$importer->importPages( new \ArrayIterator( [
			new EntityPage( 'first', 'first', 1, 100, 'foo' ),
			new EntityPage( 'second', 'second', 2, 200, 'foo' ),
			new EntityPage( 'third', 'third', 3, 300, 'foo' )
		] ) );
	}

}

