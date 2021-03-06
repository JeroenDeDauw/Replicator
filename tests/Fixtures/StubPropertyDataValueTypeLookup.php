<?php

namespace Tests\Queryr\Replicator\Fixtures;

use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\QueryEngine\PropertyDataValueTypeLookup;

class StubPropertyDataValueTypeLookup implements PropertyDataValueTypeLookup {

	public function getDataValueTypeForProperty( PropertyId $propertyId ): string {
		return 'number';
	}

}