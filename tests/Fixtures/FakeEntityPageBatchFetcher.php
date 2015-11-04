<?php

namespace Tests\Queryr\Replicator\Fixtures;

use Queryr\Replicator\EntitySource\EntityPageBatchFetcher;
use Queryr\Replicator\Model\EntityPage;

class FakeEntityPageBatchFetcher implements EntityPageBatchFetcher {

	private $idsToIgnore;

	public function __construct( array $idsToIgnore = [] ) {
		$this->idsToIgnore = $idsToIgnore;
	}

	/**
	 * @param string[] $entityIds
	 *
	 * @return EntityPage[]
	 */
	public function fetchEntityPages( array $entityIds ) {
		return array_diff( $entityIds, $this->idsToIgnore );
	}

}
