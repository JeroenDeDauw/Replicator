<?php

namespace Queryr\Replicator\Importer;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Queryr\Replicator\Model\EntityPage;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;

class PageImporter {

	private $entityDeserializer;
	private $reporter;

	private $entityHandlers;
	private $entityPageHandlers;

	/**
	 * @param Deserializer $entityDeserializer
	 * @param EntityHandler[] $entityHandlers
	 * @param EntityPageHandler[] $entityPageHandlers
	 * @param PageImportReporter $reporter
	 */
	public function __construct( Deserializer $entityDeserializer, array $entityHandlers,
		array $entityPageHandlers, PageImportReporter $reporter ) {

		$this->entityDeserializer = $entityDeserializer;
		$this->reporter = $reporter;

		$this->entityHandlers = $entityHandlers;
		$this->entityPageHandlers = $entityPageHandlers;
	}

	public function import( EntityPage $entityPage ) {
		$this->reporter->started( $entityPage );

		try {
			$this->doImport( $entityPage );

			$this->reporter->endedSuccessfully();
		}
		catch ( \Exception $ex ) {
			$this->reporter->endedWithError( $ex );
		}
	}

	private function doImport( EntityPage $entityPage ) {
		if ( $this->thereAreHandlers() ) {
			$entity = $this->doDeserializeStep( $entityPage );

			$this->invokeEntityPageHandlers( $entity, $entityPage );
			$this->invokeEntityHandlers( $entity );
		}
	}

	private function thereAreHandlers(): bool {
		return !empty( $this->entityHandlers ) || !empty( $this->entityPageHandlers );
	}

	private function doDeserializeStep( EntityPage $entityPage ) {
		$this->reporter->stepStarted( 'Deserializing' );
		$entity = $this->entityFromEntityPage( $entityPage );
		$this->reporter->stepCompleted();

		return $entity;
	}

	private function invokeEntityPageHandlers( EntityDocument $entity, EntityPage $entityPage ) {
		foreach ( $this->entityPageHandlers as $pageHandler ) {
			$this->reporter->stepStarted( $pageHandler->getHandlingMessage( $entity ) );
			$pageHandler->handleEntityPage( $entity, $entityPage );
			$this->reporter->stepCompleted();
		}
	}

	private function invokeEntityHandlers( EntityDocument $entity ) {
		foreach ( $this->entityHandlers as $entityHandler ) {
			$this->reporter->stepStarted( $entityHandler->getHandlingMessage( $entity ) );
			$entityHandler->handleEntity( $entity );
			$this->reporter->stepCompleted();
		}
	}

	/**
	 * @param EntityPage $entityPage
	 * @return Item
	 * @throws DeserializationException
	 */
	private function entityFromEntityPage( EntityPage $entityPage ) {
		return $this->entityDeserializer->deserialize(
			json_decode( $entityPage->getEntityJson(), true )
		);
	}

	public function setReporter( PageImportReporter $reporter ) {
		$this->reporter = $reporter;
	}

	public function getReporter() {
		return $this->reporter;
	}

}
