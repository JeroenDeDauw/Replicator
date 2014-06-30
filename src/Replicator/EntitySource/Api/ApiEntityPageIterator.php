<?php

namespace Queryr\Replicator\EntitySource\Api;

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
	private $current;

	/**
	 * @var EntityPagesFetcher
	 */
	private $fetcher;

	/**
	 * @var EntityId[]
	 */
	private $pagesToFetch;

	/**
	 * @param EntityPagesFetcher $fetcher
	 * @param EntityId[] $pagesToFetch
	 */
	public function __construct( EntityPagesFetcher $fetcher, array $pagesToFetch ) {
		$this->fetcher = $fetcher;
		$this->pagesToFetch = $pagesToFetch;
	}

	/**
	 * @return EntityPage|null
	 */
	public function current() {
		return $this->current;
	}

	public function next() {
		$pages = $this->fetcher->fetchEntityPages( [ current( $this->pagesToFetch ) ] );

		$this->current = empty( $pages ) ? null : $pages[0];
		next( $this->pagesToFetch );
	}

	/**
	 * @return int
	 */
	public function key() {
		return key( $this->pagesToFetch );
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->current !== null;
	}

	public function rewind() {
		reset( $this->pagesToFetch );
		$this->next();
	}

}
