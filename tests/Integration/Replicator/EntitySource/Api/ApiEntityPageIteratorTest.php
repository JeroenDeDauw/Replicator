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

	public function testGivenFiveIdsAndBatchSizeThree_twoCallsAreMade() {
		$fetcher = $this->newFetcherMock();

		$fetcher->expects( $this->exactly( 2 ) )
			->method( 'fetchEntityPages' )
			->will( $this->returnArgument( 0 ) );

		$iterator = new ApiEntityPageIterator(
			$fetcher,
			[ [ 'Q1', 'Q2', 'Q3' ], [ 'Q4', 'Q5' ] ]
		);

		$this->assertSame(
			[ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ],
			iterator_to_array( $iterator )
		);
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