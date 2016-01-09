<?php

namespace Queryr\Replicator\Plugin;

use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface EntityHandlerPlugin {

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