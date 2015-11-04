<?php

namespace Queryr\Replicator;

use InvalidArgumentException;
use Iterator;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParser;
use Wikibase\DataModel\Entity\EntityIdParsingException;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\Entity\PropertyId;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityIdListNormalizer {

	private $idParser;

	public function __construct( EntityIdParser $idParser ) {
		$this->idParser = $idParser;
	}

	/**
	 * @param string[] $ids
	 *
	 * @return Iterator|EntityId
	 * @throws InvalidArgumentException
	 */
	public function getNormalized( array $ids ) {
		foreach ( $ids as $id ) {
			if ( strpos( $id, '-' ) !== false ) {
				foreach ( $this->getRange( $id ) as $resultId ) {
					yield $resultId;
				}
			}
			else {
				yield $this->getParsedId( $id );
			}
		}
	}

	private function getRange( $id ) {
		$parts = explode( '-', $id, 2 );

		$startId = $this->getParsedId( $parts[0] );
		$endId = $this->getParsedId( $parts[1] );

		if ( $startId->getEntityType() !== $endId->getEntityType() ) {
			throw new InvalidArgumentException( 'Entity ids in the same range need to be of the same type' );
		}

		if ( !( $startId instanceof ItemId ) && !( $startId instanceof PropertyId ) ) {
			throw new InvalidArgumentException( 'ID type is not supported' );
		}
		/**
		 * @var ItemId|PropertyId $endId
		 */

		if ( $startId->getNumericId() > $endId->getNumericId()  ) {
			throw new InvalidArgumentException( 'The end of the range needs to be bigger than its start' );
		}

		$numericId = $endId->getNumericId();
		for ( $i = $startId->getNumericId(); $i <= $numericId; $i++ ) {
			yield $startId::newFromNumber( $i );
		}
	}

	private function getParsedId( $id ) {
		try {
			return $this->idParser->parse( $id );
		}
		catch ( EntityIdParsingException $ex ) {
			throw new InvalidArgumentException( $ex->getMessage(), 0, $ex );
		}
	}

}
