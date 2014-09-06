<?php

namespace Queryr\Replicator;

use Wikibase\DataModel\Entity\Item;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ItemTypeExtractor implements \Queryr\EntityStore\ItemTypeExtractor {

	/**
	 * Returns the type (instance of) of the item as the numeric part of an item id.
	 *
	 * @param Item $item
	 *
	 * @return int|null
	 */
	public function getTypeOfItem( Item $item ) {
		// TODO
		return 1;
	}

}
