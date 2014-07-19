<?php

namespace Tests\Queryr\Replicator\Importer\Console;

use BatchingIterator\BatchingIterator;
use Queryr\Replicator\EntitySource\BatchingEntityPageFetcher;
use Queryr\Replicator\EntitySource\EntityPageBatchFetcher;
use Queryr\Replicator\Model\EntityPage;

/**
 * @covers Queryr\Replicator\EntitySource\Api\ApiEntityPageIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiEntityPageIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testIterationOverEmptyIterator() {
		$iterator = $this->newFakeIterator( [], [] );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

	private function newFakeIterator( array $pages, array $pagesToFetch ) {
		return new BatchingIterator( new BatchingEntityPageFetcher(
			new FakeEntityPagesFetcher( $pages ),
			$pagesToFetch
		) );
	}

	public function testWhenNoPagesAreFound_iteratorIsEmpty() {
		$iterator = $this->newFakeIterator( [], [ 'Q1', 'Q2' ] );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

	/**
	 * @dataProvider batchProvider
	 */
	public function testCorrectAmountOfCallsAreMadeToTheBatchFetcher( $ids, $maxBatchSize, $expectedCallCount ) {
		$fetcher = $this->newFetcherMock();

		$fetcher->expects( $this->exactly( $expectedCallCount ) )
			->method( 'fetchEntityPages' )
			->will( $this->returnArgument( 0 ) );

		$iterator = new BatchingIterator( new BatchingEntityPageFetcher(
			$fetcher,
			$ids
		) );

		$iterator->setMaxBatchSize( $maxBatchSize );

		$this->assertSame(
			$ids,
			iterator_to_array( $iterator )
		);
	}

	private function newFetcherMock() {
		return $this->getMockBuilder( 'Queryr\Replicator\EntitySource\EntityPageBatchFetcher' )
			->disableOriginalConstructor()->getMock();
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

}

class FakeEntityPagesFetcher implements EntityPageBatchFetcher {

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