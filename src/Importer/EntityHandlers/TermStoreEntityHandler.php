<?php

namespace Queryr\Replicator\Importer\EntityHandlers;

use Queryr\Replicator\Importer\EntityHandler;
use Queryr\TermStore\TermStoreWriter;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Term\FingerprintProvider;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class TermStoreEntityHandler implements EntityHandler {

	private $tsWriter;

	public function __construct( TermStoreWriter $tsWriter ) {
		$this->tsWriter = $tsWriter;
	}

	public function handleEntity( EntityDocument $entity ) {
		if ( $entity instanceof FingerprintProvider ) {
			$this->tsWriter->storeEntityFingerprint(
				$entity->getId(),
				$entity->getFingerprint()
			);
		}
	}

	public function getHandlingMessage( EntityDocument $entity ): string {
		return 'Inserting into Term store';
	}

}