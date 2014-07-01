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
	private $pagesToFetch;

	/**
	 * @var int
	 */
	private $batchSize;

	/**
	 * @var mixed
	 */
	private $key;

	/**
	 * @param EntityPagesFetcher $fetcher
	 * @param EntityId[] $pagesToFetch
	 * @param int $batchSize
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( EntityPagesFetcher $fetcher, array $pagesToFetch, $batchSize = 1 ) {
		$this->fetcher = $fetcher;
		$this->pagesToFetch = $pagesToFetch;

		if ( !is_int( $batchSize ) || $batchSize < 1 ) {
			throw new InvalidArgumentException( '$batchSize should be an int greater than 0' );
		}

		$this->batchSize = $batchSize;
	}

	/**
	 * @return EntityPage|null
	 */
	public function current() {
		return $this->current;
	}

	public function next() {
		$this->key = key( $this->pagesToFetch );
		$currentId = current( $this->pagesToFetch );

		if ( $currentId === false ) {
			$this->current = null;
		}
		else {
			$pages = $this->fetcher->fetchEntityPages( [ $currentId ] );
			next( $this->pagesToFetch );

			if ( empty( $pages ) ) {
				$this->next();
			}
			else {
				$this->current = $pages[0];
			}
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
		reset( $this->pagesToFetch );
		$this->next();
	}

}
