<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use Queryr\Replicator\EntitySource\Api\ApiEntityPageIterator;
use Queryr\Replicator\EntitySource\Api\EntityPagesFetcher;
use Queryr\Replicator\Model\EntityPage;

/**
 * @covers Queryr\Replicator\EntitySource\Api\ApiEntityPageIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiEntityPageIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testIterationOverEmptyIterator() {
		$iterator = new ApiEntityPageIterator( new FakeEntityPagesFetcher(), [] );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

	private function newFetcherMock() {
		return $this->getMockBuilder( 'Queryr\Replicator\EntitySource\Api\EntityPagesFetcher' )
			->disableOriginalConstructor()->getMock();
	}

	public function testGivenThreeIds_iteratorMakesTwoCallsAndIsSizeThree() {
		$iterator = new ApiEntityPageIterator(
			new FakeEntityPagesFetcher( [
				'Q1' => 1,
				'Q2' => 2,
				'Q3' => 3,
			] ),
			[ 'Q1', 'Q2', 'Q3' ]
		);

		$this->assertSame( [ 1, 2, 3 ], iterator_to_array( $iterator ) );
	}

	public function testWhenNoPagesAreFound_iteratorIsEmpty() {
		$fetcher = $this->newFetcherMock();

		$fetcher->expects( $this->exactly( 2 ) )
			->method( 'fetchEntityPages' )
			->will( $this->returnValue( [] ) );

		$iterator = new ApiEntityPageIterator( $fetcher, [ 'Q1', 'Q2' ] );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

	/**
	 * @dataProvider batchProvider
	 */
	public function testGivenFiveIdsAndBatchSizeThree_twoCallsAreMade( $ids, $maxBatchSize, $expectedCallCount ) {
		$fetcher = $this->newFetcherMock();

		$fetcher->expects( $this->exactly( $expectedCallCount ) )
			->method( 'fetchEntityPages' )
			->will( $this->returnArgument( 0 ) );

		$iterator = new ApiEntityPageIterator(
			$fetcher,
			$ids,
			$maxBatchSize
		);

		$this->assertSame(
			$ids,
			iterator_to_array( $iterator )
		);
	}

	public function batchProvider() {
		return [
			[
				[ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ],
				5,
				1
			],

			[
				[ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ],
				1,
				5
			],

			[
				[ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ],
				2,
				3
			],

			[
				[ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ],
				3,
				2
			],

			[
				[ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ],
				10,
				1
			],

			[
				[ 'Q1', 'Q2', 'Q3', 'Q4' ],
				2,
				2
			],
		];
	}

	public function testGivenInvalidBatchSize_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		new ApiEntityPageIterator( $this->newFetcherMock(), [ 'Q1', 'Q2' ], -5 );
	}

}

class FakeEntityPagesFetcher extends EntityPagesFetcher {

	private $pages;

	public function __construct( array $pages = [] ) {
		$this->pages = $pages;
	}

	/**
	 * @param string[] $entityIds
	 * @return EntityPage[]
	 */
	public function fetchEntityPages( array $entityIds ) {
		$pages = [];

		foreach ( $entityIds as $entityId ) {
			if ( array_key_exists( $entityId, $this->pages ) ) {
				$pages[] = $this->pages[$entityId];
			}
		}

		return $pages;
	}

}