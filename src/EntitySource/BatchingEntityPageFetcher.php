<?php

namespace Queryr\Replicator\EntitySource;

use BatchingIterator\BatchingFetcher;
use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class BatchingEntityPageFetcher implements BatchingFetcher {

	/**
	 * @var EntityPageBatchFetcher
	 */
	private $batchFetcher;

	/**
	 * @var string[]
	 */
	private $pagesToFetch;

	/**
	 * @var int
	 */
	private $position;

	public function __construct( EntityPageBatchFetcher $batchFetcher, array $pagesToFetch ) {
		$this->batchFetcher = $batchFetcher;
		$this->pagesToFetch = array_unique( $pagesToFetch );
		$this->rewind();
	}

	public function addPagesToFetch( array $pagesToFetch ) {
		$this->pagesToFetch = array_unique( array_merge( $this->pagesToFetch, $pagesToFetch ) );
	}

	/**
	 * @see BatchingFetcher::fetchNext
	 *
	 * @param int $maxFetchCount
	 *
	 * @return EntityPage[]
	 */
	public function fetchNext( $maxFetchCount ) {
		$idsInBatch = array_slice( $this->pagesToFetch, $this->position, $maxFetchCount );
		$this->position = min( $this->position + $maxFetchCount, count( $this->pagesToFetch ) );

		if ( empty( $idsInBatch ) ) {
			return [];
		}

		return $this->batchFetcher->fetchEntityPages( $idsInBatch );
	}

	/**
	 * @see BatchingFetcher::rewind
	 */
	public function rewind() {
		$this->position = 0;
	}

}
