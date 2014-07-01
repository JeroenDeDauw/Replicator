<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use Queryr\Replicator\EntitySource\Api\ApiEntityPageIterator;

/**
 * @covers Queryr\Replicator\EntitySource\Api\ApiEntityPageIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiEntityPageIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testIterationOverEmptyIterator() {
		$iterator = new ApiEntityPageIterator( $this->newFetcherMock(), [] );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

	private function newFetcherMock() {
		return $this->getMockBuilder( 'Queryr\Replicator\EntitySource\Api\EntityPagesFetcher' )
			->disableOriginalConstructor()->getMock();
	}

	public function testGivenTwoIds_iteratorMakesTwoCallsAndIsSizeTwo() {
		$fetcher = $this->newFetcherMock();

		$fetcher->expects( $this->exactly( 2 ) )
			->method( 'fetchEntityPages' )
			->will( $this->returnValue( [ 42 ] ) );

		$iterator = new ApiEntityPageIterator( $fetcher, [ 'Q1', 'Q2' ] );

		$this->assertSame( [ 42, 42 ], iterator_to_array( $iterator ) );
	}

	public function testGivenInvalidBatchSize_exceptionIsThrown() {
		$this->setExpectedException( 'InvalidArgumentException' );
		new ApiEntityPageIterator( $this->newFetcherMock(), [ 'Q1', 'Q2' ], -5 );
	}

	public function testWhenNoPagesAreFound_iteratorIsEmpty() {
		$fetcher = $this->newFetcherMock();

		$fetcher->expects( $this->exactly( 2 ) )
			->method( 'fetchEntityPages' )
			->will( $this->returnValue( [] ) );

		$iterator = new ApiEntityPageIterator( $fetcher, [ 'Q1', 'Q2' ] );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

//	public function testGivenFiveIdsAndBatchSizeThree_twoCallsAreMade() {
//		$fetcher = $this->getMockBuilder( 'Queryr\Replicator\EntitySource\Api\EntityPagesFetcher' )
//			->disableOriginalConstructor()->getMock();
//
//		$fetcher->expects( $this->exactly( 2 ) )
//			->method( 'fetchEntityPages' )
//			->will( $this->returnArgument( 0 ) );
//
//		$iterator = new ApiEntityPageIterator( $fetcher, [ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ] );
//
//		$this->assertSame(
//			[ [ 'Q1', 'Q2', 'Q3' ], [ 'Q4', 'Q5' ] ],
//			iterator_to_array( $iterator )
//		);
//	}

}