<?php

namespace QueryR\Replicator;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;

class StubPropertyDataValueTypeLookup implements PropertyDataValueTypeLookup {

	public function getDataValueTypeForProperty( PropertyId $propertyId ) {
		return 'number';
	}

}