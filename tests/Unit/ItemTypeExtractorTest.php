<?php

namespace Tests\Queryr\Replicator;

use Queryr\Replicator\ItemTypeExtractor;
use Wikibase\DataModel\Entity\Item;

/**
 * @covers Queryr\Replicator\ItemTypeExtractor
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ItemTypeExtractorTest extends \PHPUnit_Framework_TestCase {

	public function testGivenEmptyItem_nullIsReturned() {
		$extractor = new ItemTypeExtractor();

		$this->assertNull( $extractor->getTypeOfItem( Item::newEmpty() ) );
	}

}