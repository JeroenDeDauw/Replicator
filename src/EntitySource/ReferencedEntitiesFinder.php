<?php

namespace Queryr\Replicator\EntitySource;

use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Snak\PropertyValueSnak;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReferencedEntitiesFinder {

	/**
	 * @param Item $item
	 *
	 * @return EntityId[]
	 */
	public function findForItem( Item $item ): array {
		$references = [];

		foreach ( $item->getStatements()->toArray() as $statement ) {
			$references[] = $statement->getPropertyId();

			$mainSnak = $statement->getMainSnak();

			if ( $mainSnak instanceof PropertyValueSnak && $mainSnak->getDataValue() instanceof EntityIdValue ) {
				$references[] = $mainSnak->getDataValue()->getEntityId();
			}
		}

		return array_unique( $references );
	}

}