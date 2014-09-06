<?php

namespace Queryr\Replicator;

use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyValueSnak;
use Wikibase\DataModel\Statement\Statement;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ItemTypeExtractor implements \Queryr\EntityStore\ItemTypeExtractor {

	/**
	 * @var PropertyId
	 */
	private $instanceOfPropertyId;

	public function __construct() {
		$this->instanceOfPropertyId = new PropertyId( 'P31' );
	}

	/**
	 * Returns the type (instance of) of the item as the numeric part of an item id.
	 *
	 * @param Item $item
	 *
	 * @return int|null
	 */
	public function getTypeOfItem( Item $item ) {
		/**
		 * @var Statement $statement
		 */
		foreach ( $item->getStatements() as $statement ) {
			if ( $statement->getPropertyId()->equals( $this->instanceOfPropertyId ) ) {
				$valueSnak = $statement->getMainSnak();

				if ( $valueSnak instanceof PropertyValueSnak ) {
					$value = $valueSnak->getDataValue();

					if ( $value instanceof EntityIdValue ) {

						$itemId = $value->getEntityId();

						if ( $itemId instanceof ItemId ) {
							return $itemId->getNumericId();
						}
					}
				}
			}
		}

		return null;
	}

}
