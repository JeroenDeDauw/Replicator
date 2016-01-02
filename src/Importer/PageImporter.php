<?php

namespace Queryr\Replicator\Importer;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Queryr\EntityStore\EntityStore;
use Queryr\Replicator\Importer\EntityHandlers\EntityStoreEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\QueryEngineEntityHandler;
use Queryr\Replicator\Importer\EntityHandlers\TermStoreEntityHandler;
use Queryr\Replicator\Model\EntityPage;
use Queryr\TermStore\TermStoreWriter;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\QueryEngine\QueryStoreWriter;

class PageImporter {

	private $entityDeserializer;
	private $reporter;

	private $entityHandlers;
	private $entityPageHandlers;

	public function __construct( EntityStore $entityStore, Deserializer $entityDeserializer,
		QueryStoreWriter $queryStoreWriter, PageImportReporter $reporter, TermStoreWriter $termStore ) {

		$this->entityDeserializer = $entityDeserializer;
		$this->reporter = $reporter;

		$this->a( [
			new TermStoreEntityHandler( $termStore ),
			new QueryEngineEntityHandler( $queryStoreWriter )
		], [
			new EntityStoreEntityHandler( $entityStore )
		] );
	}

	/**
	 * @param EntityHandler[] $entityHandlers
	 * @param EntityPageHandler[] $entityPageHandlers
	 */
	public function a( array $entityHandlers, array $entityPageHandlers ) {
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
