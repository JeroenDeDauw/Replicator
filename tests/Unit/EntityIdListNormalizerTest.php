<?php

namespace Tests\Queryr\Replicator;

use Queryr\Replicator\EntityIdListNormalizer;
use Wikibase\DataModel\Entity\BasicEntityIdParser;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @covers Queryr\Replicator\EntityIdListNormalizer
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityIdListNormalizerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var EntityIdListNormalizer
	 */
	private $normalizer;

	public function setUp() {
		$this->normalizer = new EntityIdListNormalizer( new BasicEntityIdParser() );
	}

	public function testGetNormalizedReturnsIterator() {
		$this->assertInstanceOf( 'Iterator', $this->normalizer->getNormalized( [] ) );
	}

	private function assertInputResultsInNormalization( array $input, array $normalization ) {
		$this->assertEquals(
			$normalization,
			iterator_to_array( $this->normalizer->getNormalized( $input ) )
		);
	}

	public function testEmptyListIsNormalizedToEmptyList() {
		$this->assertInputResultsInNormalization( [], [] );
	}

	public function testItemIdsGetParsed() {
		$this->assertInputResultsInNormalization(
			[ 'Q321', 'Q123' ],
			[ new ItemId( 'Q321' ), new ItemId( 'Q123' ) ]
		);
	}

	public function testMixedIdsGetParsed() {
		$this->assertInputResultsInNormalization(
			[ 'P321', 'Q123' ],
			[ new PropertyId( 'P321' ), new ItemId( 'Q123' ) ]
		);
	}

	public function testItemRangeGetsExpanded() {
		$this->assertInputResultsInNormalization(
			[ 'P321', 'Q123-Q125', 'P42' ],
			[
				new PropertyId( 'P321' ),
				new ItemId( 'Q123' ),
				new ItemId( 'Q124' ),
				new ItemId( 'Q125' ),
				new PropertyId( 'P42' )
			]
		);
	}

	public function testMultipleRangesGetExpanded() {
		$this->assertInputResultsInNormalization(
			[ 'Q1', 'Q100-Q102', 'P400-P401', 'Q42' ],
			[
				new ItemId( 'Q1' ),
				new ItemId( 'Q100' ),
				new ItemId( 'Q101' ),
				new ItemId( 'Q102' ),
				new PropertyId( 'P400' ),
				new PropertyId( 'P401' ),
				new ItemId( 'Q42' ),
			]
		);
	}

	public function testGivenNonEntityId_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		iterator_to_array( $this->normalizer->getNormalized( [ 'kittens' ] ) );
	}

	public function testGivenMixedRange_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		iterator_to_array( $this->normalizer->getNormalized( [ 'Q1-P3' ] ) );
	}

	public function testRangeWithInvalidId_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		iterator_to_array( $this->normalizer->getNormalized( [ 'Q123-kittens' ] ) );
	}

	public function testRangeInWrongDirection_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		iterator_to_array( $this->normalizer->getNormalized( [ 'Q102-Q100' ] ) );
	}

}

