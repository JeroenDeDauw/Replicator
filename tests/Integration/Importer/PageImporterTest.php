<?php

namespace Tests\Queryr\Replicator\Integration\Importer;

use Queryr\Replicator\EntitySource\Api\GetEntitiesInterpreter;
use Queryr\Replicator\Importer\EntityHandlers\EntityStoreEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\QueryEngineEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\TermStoreEntityHandler;
use Queryr\Replicator\Importer\PageImporter;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageImporterTest extends \PHPUnit_Framework_TestCase {

	public function testWhenInsertingBerlin_entityStoreFieldsAreSet() {
		$factory = TestEnvironment::newInstance()->getFactory();

		$pageImporter = new PageImporter(
			$factory->newLegacyEntityDeserializer(),
			[
				new TermStoreEntityHandler( $factory->newTermStoreWriter() ),
				new QueryEngineEntityHandler( $factory->newQueryStoreWriter() )
			],
			[
				new EntityStoreEntityHandler( $factory->newEntityStore() )
			],
			$this->getMock( 'Queryr\Replicator\Importer\PageImportReporter' )
		);

		$jsonString = file_get_contents( __DIR__ . '/../../data/api/Q64.json' );
		$entityPages = ( new GetEntitiesInterpreter() )->getEntityPagesFromResult( $jsonString );

		foreach ( $entityPages as $entityPage ) {
			$pageImporter->import( $entityPage );
		}

		$itemStore = $factory->newItemStore();

		$itemRow = $itemStore->getItemRowByNumericItemId( 64 );

		$this->assertSame( 64, $itemRow->getNumericItemId() );
		$this->assertSame( 'Q64', $itemRow->getPageTitle() );
		$this->assertSame( 515, $itemRow->getItemType() );
		$this->assertSame( 'Berlin', $itemRow->getEnglishLabel() );
		$this->assertSame( 'Berlin', $itemRow->getEnglishWikipediaTitle() );

		$this->assertInternalType( 'array', json_decode( $itemRow->getItemJson(), true ) );
	}

}
