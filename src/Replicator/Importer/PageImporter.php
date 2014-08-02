<?php

namespace Queryr\Replicator\Importer;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Queryr\EntityStore\EntityStore;
use Queryr\EntityStore\ItemRow;
use Queryr\EntityStore\PropertyRow;
use Queryr\Replicator\Model\EntityPage;
use Queryr\TermStore\TermStore;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\QueryEngine\QueryStoreWriter;

class PageImporter {

	private $entityStore;
	private $entityDeserializer;
	private $queryStoreWriter;
	private $reporter;
	private $termStore;

	/**
	 * @var Entity
	 */
	private $entity;

	public function __construct( EntityStore $entityStore, Deserializer $entityDeserializer,
		QueryStoreWriter $queryStoreWriter, PageImportReporter $reporter, TermStore $termStore ) {

		$this->entityStore = $entityStore;
		$this->entityDeserializer = $entityDeserializer;
		$this->queryStoreWriter = $queryStoreWriter;
		$this->reporter = $reporter;
		$this->termStore = $termStore;
	}

	public function import( EntityPage $entityPage ) {
		$this->reporter->started( $entityPage );

		try {
			$this->doDeserializeStep( $entityPage );

			if ( !in_array( $this->entity->getType(), [ 'item', 'property' ] ) ) {
				return;
			}

			$this->doDumpStoreStep( $entityPage );
			$this->doTermStoreStep();
			$this->doQueryStoreStep();

			$this->reporter->endedSuccessfully();
		}
		catch ( \Exception $ex ) {
			$this->reporter->endedWithError( $ex );
		}
	}

	private function doDeserializeStep( EntityPage $entityPage ) {
		$this->reporter->stepStarted( 'Deserializing' );
		$this->entity = $this->entityFromEntityPage( $entityPage );
		$this->reporter->stepCompleted();
	}

	private function doQueryStoreStep() {
		$this->reporter->stepStarted( 'Inserting into Query store' );
		$this->insertIntoQueryStore();
		$this->reporter->stepCompleted();
	}

	private function doDumpStoreStep( EntityPage $entityPage ) {
		$this->reporter->stepStarted( 'Inserting into Dump store' );
		$this->insertIntoDumpStore( $entityPage );
		$this->reporter->stepCompleted();
	}

	private function doTermStoreStep() {
		$this->reporter->stepStarted( 'Inserting into Term store' );
		$this->insertIntoTermStore();
		$this->reporter->stepCompleted();
	}

	private function insertIntoQueryStore() {
		$this->queryStoreWriter->insertEntity( $this->entity );
	}

	private function insertIntoTermStore() {
		$this->termStore->storeEntityFingerprint(
			$this->entity->getId(),
			$this->entity->getFingerprint()
		);
	}

	private function insertIntoDumpStore( EntityPage $entityPage ) {
		if ( $this->entity->getType() === 'item' ) {
			$itemRow = $this->itemRowFromEntityPage( $entityPage );
			$this->entityStore->storeItemRow( $itemRow );
		}
		else if ( $this->entity->getType() === 'property' ) {
			$propertyRow = $this->propertyRowFromEntityPage( $entityPage );
			$this->entityStore->storePropertyRow( $propertyRow );
		}
	}

	private function itemRowFromEntityPage( EntityPage $entityPage ) {
		return new ItemRow(
			$this->entity->getId()->getNumericId(),
			$entityPage->getEntityJson(),
			$entityPage->getTitle(),
			$entityPage->getRevisionId(),
			$entityPage->getRevisionTime()
		);
	}

	private function propertyRowFromEntityPage( EntityPage $entityPage ) {
		return new PropertyRow(
			$this->entity->getId()->getNumericId(),
			$entityPage->getEntityJson(),
			$entityPage->getTitle(),
			$entityPage->getRevisionId(),
			$entityPage->getRevisionTime(),
			$this->entity->getDataTypeId()
		);
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
