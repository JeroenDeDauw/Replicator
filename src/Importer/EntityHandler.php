<?php

namespace Queryr\Replicator\Importer;

use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityHandler {

	/**
	 * @param EntityDocument $entity
	 * @throws \Exception
	 */
	public function handleEntity( EntityDocument $entity );

	/**
	 * @param EntityDocument $entity
	 * @return string
	 */
	public function getHandlingMessage( EntityDocument $entity ): string;

}