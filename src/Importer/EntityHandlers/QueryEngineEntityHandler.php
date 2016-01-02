<?php

namespace Queryr\Replicator\Importer\EntityHandlers;

use Queryr\Replicator\Importer\EntityHandler;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\QueryEngine\QueryStoreWriter;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class QueryEngineEntityHandler implements EntityHandler {

	private $queryStoreWriter;

	public function __construct( QueryStoreWriter $queryStoreWriter ) {
		$this->queryStoreWriter = $queryStoreWriter;
	}

	public function handleEntity( EntityDocument $entity ) {
		$this->queryStoreWriter->updateEntity( $entity );
	}

	public function getHandlingMessage( EntityDocument $entity ): string {
		return 'Inserting into Query store';
	}

}