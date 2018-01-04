<?php

namespace Tests\Queryr\Replicator\EntitySource;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\EntitySource\BatchingEntityPageFetcher;
use Tests\Queryr\Replicator\Fixtures\FakeEntityPageBatchFetcher;

/**
 * @covers \Queryr\Replicator\EntitySource\BatchingEntityPageFetcher
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BatchingEntityPageFetcherTest extends TestCase {

	public function testGivenNoPagesToFetch_noneAreReturned() {
		$fetcher = $this->newFetcherForPages( [] );

		$this->assertSame( [], $fetcher->fetchNext( 10 ) );
		$this->assertSame( [], $fetcher->fetchNext( 10 ) );
	}

	private function newFetcherForPages( array $entityIds, array $idsToIgnore = [] ) {
		return new BatchingEntityPageFetcher( new FakeEntityPageBatchFetcher( $idsToIgnore ), $entityIds );
	}

	public function testRewind() {
		$fetcher = $this->newFetcherForPages( [ 'Q1', 'Q2', 'Q3' ] );

		$fetcher->fetchNext( 2 );
		$fetcher->rewind();
		$this->assertSame( [ 'Q1', 'Q2' ], $fetcher->fetchNext( 2 ) );
	}

	public function testProgressionThroughArray() {
		$fetcher = $this->newFetcherForPages( [ 'Q1', 'Q2', 'Q3' ] );

		$this->assertSame( [ 'Q1', 'Q2' ], $fetcher->fetchNext( 2 ) );
		$this->assertSame( [ 'Q3' ], $fetcher->fetchNext( 2 ) );
		$this->assertSame( [], $fetcher->fetchNext( 2 ) );
	}

	public function testWhenAtInitialPosition_addingNewPagesWorks() {
		$fetcher = $this->newFetcherForPages( [ 'Q1', 'Q2', 'Q3' ] );

		$fetcher->addPagesToFetch( [ 'Q4' ] );

		$this->assertSame( [ 'Q1', 'Q2', 'Q3', 'Q4' ], $fetcher->fetchNext( 5 ) );
	}

	public function testWhenPartiallyIterated_addingNewPagesWorks() {
		$fetcher = $this->newFetcherForPages( [ 'Q1', 'Q2', 'Q3' ] );

		$fetcher->fetchNext( 2 );
		$fetcher->addPagesToFetch( [ 'Q4' ] );

		$this->assertSame( [ 'Q3', 'Q4' ], $fetcher->fetchNext( 2 ) );
	}

	public function testWhenFetchedBeyondTheLastPage_newlyAddedPagesAreStillFetchedOnNextCall() {
		$fetcher = $this->newFetcherForPages( [ 'Q1', 'Q2' ] );

		$fetcher->fetchNext( 3 );
		$fetcher->addPagesToFetch( [ 'Q3', 'Q4' ] );

		$this->assertSame( [ 'Q3', 'Q4' ], $fetcher->fetchNext( 3 ) );
	}

	public function testWhenPartiallyIterated_additionOfDuplicatesGetsIgnored() {
		$fetcher = $this->newFetcherForPages( [ 'Q1', 'Q2', 'Q3' ] );

		$fetcher->fetchNext( 2 );
		$fetcher->addPagesToFetch( [ 'Q1', 'Q4', 'Q3' ] );

		$this->assertSame( [ 'Q3', 'Q4' ], $fetcher->fetchNext( 3 ) );
	}

	public function testWhenFullBatchIsEmpty_theNextBatchIsRequested() {
		$fetcher = $this->newFetcherForPages(
			[ 'Q1', 'Q2', 'Q3', 'Q4', 'Q5' ], // These IDs are requested
			[ 'Q1', 'Q2', 'Q3' ] // These IDs are not found
		);

		$this->assertSame( [ 'Q4', 'Q5' ], $fetcher->fetchNext( 3 ) );
	}

}
