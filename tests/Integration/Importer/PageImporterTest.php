<?php

namespace Tests\Queryr\Replicator\Integration\Importer;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\EntitySource\Api\GetEntitiesInterpreter;
use Queryr\Replicator\Importer\EntityHandlers\EntityStoreEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\QueryEngineEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\TermStoreEntityHandler;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\ServiceFactory;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageImporterTest extends TestCase {

	public function testWhenInsertingBerlin_entityStoreFieldsAreSet() {
		$factory = TestEnvironment::newInstance()->getFactory();

		$pageImporter = new PageImporter(
			$factory->newLegacyEntityDeserializer(),
			$this->getEntityHandlers( $factory ),
			[
				new EntityStoreEntityHandler( $factory->newEntityStore() )
			],
			$this->createMock( PageImportReporter::class )
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

	private function getEntityHandlers( ServiceFactory $factory ) {
		$handlers = [
			new TermStoreEntityHandler( $factory->newTermStoreWriter() )
		];

		if ( defined( 'WIKIBASE_QUERYENGINE_VERSION' ) ) {
			$handlers[] = new QueryEngineEntityHandler( $factory->newQueryStoreWriter() );
		}

		return $handlers;
	}
}
