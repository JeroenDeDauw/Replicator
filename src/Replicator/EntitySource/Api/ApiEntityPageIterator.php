<?php

namespace Queryr\Replicator\EntitySource\Api;

use InvalidArgumentException;
use Iterator;
use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiEntityPageIterator implements Iterator {

	/**
	 * @var EntityPagesFetcher
	 */
	private $fetcher;

	/**
	 * @var int
	 */
	private $maxBatchSize;

	/**
	 * @var EntityPage|null
	 */
	private $current = null;

	/**
	 * @var string[]
	 */
	private $pageSetsToFetch;

	/**
	 * @var EntityPage[]
	 */
	private $currentBatch = [];

	/**
	 * @var int
	 */
	private $key;

	/**
	 * @param EntityPagesFetcher $fetcher
	 * @param string[] $pagesToFetch
	 * @param int $maxBatchSize
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( EntityPagesFetcher $fetcher, array $pagesToFetch, $maxBatchSize = 1 ) {
		$this->fetcher = $fetcher;
		$this->setMaxBatchSize( $maxBatchSize );

		$this->pageSetsToFetch = [];
		$this->addPagesToFetch( $pagesToFetch );
	}

	private function setMaxBatchSize( $maxBatchSize ) {
		if ( !is_int( $maxBatchSize ) || $maxBatchSize < 1 ) {
			throw new InvalidArgumentException( '$maxBatchSize should be an int bigger than 0.' );
		}

		$this->maxBatchSize = $maxBatchSize;
	}

	private function addPagesToFetch( array $pagesToFetch ) {
		$offset = 0;

		while ( $batch = array_slice( $pagesToFetch, $offset, $this->maxBatchSize ) ) {
			$this->pageSetsToFetch[] = $batch;
			$offset += count( $batch );
		}
	}

	/**
	 * @return EntityPage|null
	 */
	public function current() {
		return $this->current;
	}

	public function next() {
		$page = $this->nextPageFromBatch();

		if ( $page === null ) {
			if ( current( $this->pageSetsToFetch ) === false ) {
				$this->current = null;
			}
			else {
				$this->fetchNextBatch( current( $this->pageSetsToFetch ) );
				next( $this->pageSetsToFetch );
				$this->next();
			}
		}
		else {
			$this->current = $page;
			$this->key++;
		}
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->key;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->current !== null;
	}

	public function rewind() {
		$this->key = -1;
		reset( $this->pageSetsToFetch );
		$this->next();
	}

	private function fetchNextBatch( array $entityIds ) {
		$this->currentBatch = $this->fetcher->fetchEntityPages( $entityIds );
	}

	/**
	 * @return EntityPage|null
	 */
	private function nextPageFromBatch() {
		return array_shift( $this->currentBatch );
	}

}
