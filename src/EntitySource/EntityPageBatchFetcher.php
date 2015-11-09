<?php

namespace Queryr\Replicator\EntitySource;

use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityPageBatchFetcher {

	/**
	 * @param string[] $entityIds
	 * @return EntityPage[]
	 */
	public function fetchEntityPages( array $entityIds ): array;

}