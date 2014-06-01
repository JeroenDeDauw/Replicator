<?php

namespace Queryr\Replicator\Importer;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Queryr\Dump\Reader\Page;
use Queryr\Dump\Store\ItemRow;
use Queryr\Dump\Store\Store;
use Queryr\TermStore\TermStore;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\Item;
use Wikibase\QueryEngine\QueryStoreWriter;

class PageImporter {

	private $dumpStore;
	private $entityDeserializer;
	private $queryStoreWriter;
	private $reporter;
	private $termStore;

	/**
	 * @var Entity
	 */
	private $entity;

	public function __construct( Store $dumpStore, Deserializer $entityDeserializer,
		QueryStoreWriter $queryStoreWriter, PageImportReporter $reporter, TermStore $termStore ) {

		$this->dumpStore = $dumpStore;
		$this->entityDeserializer = $entityDeserializer;
		$this->queryStoreWriter = $queryStoreWriter;
		$this->reporter = $reporter;
		$this->termStore = $termStore;
	}

	public function import( Page $entityPage ) {
		$this->reporter->started( $entityPage );

		try {
			$this->doDeserializeStep( $entityPage );

			if ( $this->entity->getType() !== 'item' ) {
				return;
			}

			$this->doDumpStoreStep( $entityPage );
			$this->doQueryStoreStep();
			$this->doTermStoreStep();

			$this->reporter->endedSuccessfully();
		}
		catch ( \Exception $ex ) {
			$this->reporter->endedWithError( $ex );
		}
	}

	private function doDeserializeStep( Page $entityPage ) {
		$this->reporter->stepStarted( 'Deserializing' );
		$this->entity = $this->entityFromEntityPage( $entityPage );
		$this->reporter->stepCompleted();
	}

	private function doQueryStoreStep() {
		$this->reporter->stepStarted( 'Inserting into Query store' );
		$this->insertIntoQueryStore();
		$this->reporter->stepCompleted();
	}

	private function doDumpStoreStep( Page $entityPage ) {
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

	private function insertIntoDumpStore( Page $entityPage ) {
		$itemRow = $this->itemRowFromEntityPage( $entityPage );

		$this->dumpStore->storeItemRow( $itemRow );
	}

	private function itemRowFromEntityPage( Page $entityPage ) {
		$revision = $entityPage->getRevision();

		return new ItemRow(
			$this->entity->getId()->getNumericId(),
			$revision->getText(),
			$entityPage->getTitle(),
			$revision->getId(),
			$revision->getTimeStamp()
		);
	}

	/**
	 * @param Page $entityPage
	 * @return Item
	 * @throws DeserializationException
	 */
	private function entityFromEntityPage( Page $entityPage ) {
		return $this->entityDeserializer->deserialize(
			json_decode( $entityPage->getRevision()->getText(), true )
		);
	}

	public function setReporter( PageImportReporter $reporter ) {
		$this->reporter = $reporter;
	}

	public function getReporter() {
		return $this->reporter;
	}

}
