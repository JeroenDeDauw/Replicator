<?php

namespace Tests\Queryr\Replicator;

use DataValues\StringValue;
use Queryr\Replicator\ItemTypeExtractor;
use Wikibase\DataFixtures\Items\Berlin;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;

/**
 * @covers Queryr\Replicator\ItemTypeExtractor
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ItemTypeExtractorTest extends \PHPUnit_Framework_TestCase {

	const INSTANCEOF_PROP_ID = 31;

	public function testGivenEmptyItem_nullIsReturned() {
		$this->assertNull( ( new ItemTypeExtractor() )->getTypeOfItem( Item::newEmpty() ) );
	}

	public function testGivenBerlin_515isReturned() {
		$this->assertSame(
			515,
			( new ItemTypeExtractor() )->getTypeOfItem( ( new Berlin() )->newItem() )
		);
	}

	public function testGivenItemWithNoValueType_nullIsReturned() {
		$item = Item::newEmpty();

		$item->getStatements()->addNewStatement( new PropertyNoValueSnak( self::INSTANCEOF_PROP_ID ) );

		$this->assertNull( ( new ItemTypeExtractor() )->getTypeOfItem( $item ) );
	}

	public function testGivenItemWithNonIdType_nullIsReturned() {
		$item = Item::newEmpty();

		$item->getStatements()->addNewStatement( new PropertyValueSnak( self::INSTANCEOF_PROP_ID, new StringValue( 'not an id' ) ) );

		$this->assertNull( ( new ItemTypeExtractor() )->getTypeOfItem( $item ) );
	}

}