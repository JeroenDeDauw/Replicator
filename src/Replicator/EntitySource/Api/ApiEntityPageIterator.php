<?php

namespace Queryr\Replicator\EntitySource\Api;

use InvalidArgumentException;
use Iterator;
use Queryr\Replicator\Model\EntityPage;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiEntityPageIterator implements Iterator {

	/**
	 * @var EntityPage|null
	 */
	private $current = null;

	/**
	 * @var EntityPagesFetcher
	 */
	private $fetcher;

	/**
	 * @var EntityId[]
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
	 * @param EntityId[] $pageSetsToFetch
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( EntityPagesFetcher $fetcher, array $pageSetsToFetch ) {
		$this->fetcher = $fetcher;
		$this->pageSetsToFetch = [];

		foreach ( $pageSetsToFetch as $pagesToFetch ) {
			$this->pageSetsToFetch[] = (array)$pagesToFetch;
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
