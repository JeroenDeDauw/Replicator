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
		$this->pagesToFetch = $pagesToFetch;
		$this->rewind();
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
		$this->position += $maxFetchCount;

		if ( empty( $idsInBatch ) ) {
			return array();
		}

		return $this->batchFetcher->fetchEntityPages( $idsInBatch );
	}

	/**
	 * @see BatchingFetcher::rewind
	 *
	 * @since 2.0
	 */
	public function rewind() {
		$this->position = 0;
	}

}
