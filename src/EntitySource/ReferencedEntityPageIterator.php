<?php

namespace Queryr\Replicator\EntitySource;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Iterator;
use Queryr\Replicator\Model\EntityPage;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\EntityId;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReferencedEntityPageIterator implements Iterator {

	private $innerIterator;
	private $fetcher;
	private $entityDeserializer;

	/**
	 * @var ReferencedEntitiesFinder
	 */
	private $referencedEntitiesFinder;

	/**
	 * @var string[]
	 */
	private $extraEntitiesToFetch = [];

	public function __construct( Iterator $entityPageIterator, BatchingEntityPageFetcher $fetcher,
			Deserializer $entityDeserializer ) {

		$this->innerIterator = $entityPageIterator;
		$this->fetcher = $fetcher;
		$this->entityDeserializer = $entityDeserializer;

		$this->referencedEntitiesFinder = new ReferencedEntitiesFinder();
	}

	/**
	 * @return EntityPage|null
	 */
	public function current() {
		/**
		 * @var EntityPage|null $page
		 */
		$page = $this->innerIterator->current();

		if ( $page !== null ) {
			$this->addEntitiesReferencedByPage( $page );
		}

		return $page;
	}

	private function addEntitiesReferencedByPage( EntityPage $page ) {
		try {
			/**
			 * @var EntityDocument $entity
			 */
			$entity = $this->entityDeserializer->deserialize( @json_decode( $page->getEntityJson(), true ) );
		}
		catch ( DeserializationException $ex ) {
			return;
		}

		if ( !$this->entityWasFetchedDueToReference( $entity->getId() ) ) {
			$this->addEntitiesReferencedByEntity( $entity );
		}
	}

	private function entityWasFetchedDueToReference( EntityId $entityId ) {
		return in_array( $entityId->getSerialization(), $this->extraEntitiesToFetch );
	}

	private function addEntitiesReferencedByEntity( EntityDocument $entity ) {
		if ( $entity->getType() !== 'item' ) {
			return;
		}

		$referencedEntities = $this->referencedEntitiesFinder->findForItem( $entity );

		$extraEntitiesToFetch = [];

		foreach ( $referencedEntities as $referencedEntityId ) {
			$extraEntitiesToFetch[] = $referencedEntityId->getSerialization();
		}

		$this->extraEntitiesToFetch = array_merge( $this->extraEntitiesToFetch, $extraEntitiesToFetch );

		$this->fetcher->addPagesToFetch( $extraEntitiesToFetch );

	}

	public function next() {
		$this->innerIterator->next();
	}

	public function key() {
		return $this->innerIterator->key();
	}

	public function valid() {
		return $this->innerIterator->valid();
	}

	public function rewind() {
		$this->next();
		$this->extraEntitiesToFetch = [];
	}

}