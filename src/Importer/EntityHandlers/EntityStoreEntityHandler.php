<?php

namespace Queryr\Replicator\Importer\EntityHandlers;

use Queryr\EntityStore\Data\EntityPageInfo;
use Queryr\EntityStore\Data\PropertyInfo;
use Queryr\EntityStore\Data\PropertyRow;
use Queryr\EntityStore\EntityStore;
use Queryr\EntityStore\InstanceOfTypeExtractor;
use Queryr\EntityStore\ItemRowFactory;
use Queryr\Replicator\Importer\EntityPageHandler;
use Queryr\Replicator\Importer\FakingEntitySerializer;
use Queryr\Replicator\Model\EntityPage;
use Wikibase\DataModel\Entity\EntityDocument;
use Wikibase\DataModel\Entity\Item;
use Wikibase\DataModel\Entity\Property;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityStoreEntityHandler implements EntityPageHandler {

	private $entityStore;

	public function __construct( EntityStore $entityStore ) {
		$this->entityStore = $entityStore;
	}

	public function getHandlingMessage( EntityDocument $entity ): string {
		return 'Inserting into Dump store';
	}

	public function handleEntityPage( EntityDocument $entity, EntityPage $entityPage ) {
		if ( $entity instanceof Item ) {
			$itemRow = $this->itemRowFromEntityPage( $entity, $entityPage );
			$this->entityStore->storeItemRow( $itemRow );
		}
		elseif ( $entity instanceof Property ) {
			$propertyRow = $this->propertyRowFromEntityPage( $entity, $entityPage );
			$this->entityStore->storePropertyRow( $propertyRow );
		}
	}

	private function itemRowFromEntityPage( Item $item, EntityPage $entityPage ) {
		$rowFactory = new ItemRowFactory(
			new FakingEntitySerializer( json_decode( $entityPage->getEntityJson(), true ) ),
			new InstanceOfTypeExtractor()
		);

		return $rowFactory->newFromItemAndPageInfo(
			$item,
			( new EntityPageInfo() )
				->setPageTitle( $entityPage->getTitle() )
				->setRevisionId( $entityPage->getRevisionId() )
				->setRevisionTime( $entityPage->getRevisionTime() )
		);
	}

	private function propertyRowFromEntityPage( Property $property, EntityPage $entityPage ) {
		return new PropertyRow(
			$entityPage->getEntityJson(),
			new PropertyInfo(
				$property->getId()->getNumericId(),
				$entityPage->getTitle(),
				$entityPage->getRevisionId(),
				$entityPage->getRevisionTime(),
				$property->getDataTypeId()
			)
		);
	}

}