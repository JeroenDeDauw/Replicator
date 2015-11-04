<?php

namespace Tests\Queryr\Replicator\Fixtures;

use Queryr\Replicator\EntitySource\EntityPageBatchFetcher;
use Queryr\Replicator\Model\EntityPage;

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