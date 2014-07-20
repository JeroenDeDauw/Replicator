<?php

namespace Tests\Queryr\Replicator\EntitySource;

use DataValues\StringValue;
use Queryr\Replicator\EntitySource\ReferencedEntitiesFinder;
use Wikibase\DataModel\Claim\Statement;
use Wikibase\DataModel\Entity\EntityIdValue;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\DataModel\Snak\PropertyNoValueSnak;
use Wikibase\DataModel\Snak\PropertySomeValueSnak;
use Wikibase\DataModel\Snak\PropertyValueSnak;

/**
 * @covers Queryr\Replicator\EntitySource\ReferencedEntitiesFinder
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReferencedEntitiesFinderTest extends \PHPUnit_Framework_TestCase {

	private function assertFindsReferencesForItem( array $entityIds, Item $item ) {
		$this->assertEquals(
			array_values( $entityIds ),
			array_values( ( new ReferencedEntitiesFinder() )->findForItem( $item ) )
		);
	}

	public function testGivenEmptyItem_noReferencesAreFound() {
		$this->assertFindsReferencesForItem(
			[],
			Item::newEmpty()
		);
	}

	public function testGivenItemWithValuelessStatements_propertyReferencesAreFound() {
		$item = Item::newEmpty();

		$statement = new Statement( new PropertyNoValueSnak( 42 ) );
		$statement->setGuid( 'aaa' );
		$item->addClaim( $statement );

		$statement = new Statement( new PropertyNoValueSnak( 1337 ) );
		$statement->setGuid( 'bbb' );
		$item->addClaim( $statement );

		$statement = new Statement( new PropertySomeValueSnak( 42 ) );
		$statement->setGuid( 'ccc' );
		$item->addClaim( $statement );

		$this->assertFindsReferencesForItem(
			[ new PropertyId( 'P42' ), new PropertyId( 'P1337' ) ],
			$item
		);
	}

	public function testGivenItemWithValueStatements_itemReferencesAreFound() {
		$item = Item::newEmpty();

		$statement = new Statement( new PropertyValueSnak( 1, new StringValue( 'foo' ) ) );
		$statement->setGuid( 'aaa' );
		$item->addClaim( $statement );

		$statement = new Statement( new PropertyValueSnak( 1, new EntityIdValue( new ItemId( 'Q42' ) ) ) );
		$statement->setGuid( 'bbb' );
		$item->addClaim( $statement );

		$statement = new Statement( new PropertyValueSnak( 1, new EntityIdValue( new ItemId( 'Q1337' ) ) ) );
		$statement->setGuid( 'ccc' );
		$item->addClaim( $statement );

		$this->assertFindsReferencesForItem(
			[ new PropertyId( 'P1' ), new ItemId( 'Q42' ), new ItemId( 'Q1337' ) ],
			$item
		);
	}

}
