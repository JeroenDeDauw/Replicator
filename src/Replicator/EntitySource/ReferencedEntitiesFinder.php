<?php

namespace Queryr\Replicator\EntitySource;

use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\ItemId;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReferencedEntitiesFinder {

	/**
	 * @param EntityDocument $entity
	 *
	 * @return EntityId[]
	 */
	public function findForEntity( EntityDocument $entity ) {
		return [];
	}

}