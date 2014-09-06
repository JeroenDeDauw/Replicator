<?php

namespace Queryr\Replicator;

use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\PropertyId;

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
		return null;
	}

}
