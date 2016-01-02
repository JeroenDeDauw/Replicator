<?php

namespace Queryr\Replicator\Importer;

use Queryr\Replicator\Model\EntityPage;
use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityPageHandler {

	/**
	 * @param EntityDocument $entity
	 * @param EntityPage $entityPage
	 * @throws \Exception
	 */
	public function handleEntityPage( EntityDocument $entity, EntityPage $entityPage );

	/**
	 * @param EntityDocument $entity
	 * @return string
	 */
	public function getHandlingMessage( EntityDocument $entity ): string;

}