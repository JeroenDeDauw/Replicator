<?php

namespace Queryr\Replicator\Importer;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Queryr\EntityStore\Data\EntityPageInfo;
use Queryr\EntityStore\Data\PropertyInfo;
use Queryr\EntityStore\Data\PropertyRow;
use Queryr\EntityStore\EntityStore;
use Queryr\EntityStore\InstanceOfTypeExtractor;
use Queryr\EntityStore\ItemRowFactory;
use Queryr\Replicator\Model\EntityPage;
use Queryr\TermStore\TermStore;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\QueryEngine\QueryStoreWriter;

class PageImporter {

	private $entityStore;
	private $entityDeserializer;
	private $queryStoreWriter;
	private $reporter;
	private $termStore;

	/**
	 * @var EntityDocument
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
		$this->queryStoreWriter->updateEntity( $this->entity );
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
		$rowFactory = new ItemRowFactory(
			new FakingEntitySerializer( json_decode( $entityPage->getEntityJson(), true ) ),
			new InstanceOfTypeExtractor()
		);

		return $rowFactory->newFromItemAndPageInfo(
			$this->entity,
			( new EntityPageInfo() )
				->setPageTitle( $entityPage->getTitle() )
				->setRevisionId( $entityPage->getRevisionId() )
				->setRevisionTime( $entityPage->getRevisionTime() )
		);
	}

	private function propertyRowFromEntityPage( EntityPage $entityPage ) {
		return new PropertyRow(
			$entityPage->getEntityJson(),
			new PropertyInfo(
				$this->entity->getId()->getNumericId(),
				$entityPage->getTitle(),
				$entityPage->getRevisionId(),
				$entityPage->getRevisionTime(),
				$this->entity->getDataTypeId()
			)
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
