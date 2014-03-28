<?php

namespace Wikibase\Dump\Store;

use Wikibase\Database\QueryInterface\QueryInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Store {

	const ITEMS_TABLE_NAME = 'items';

	private $queryInterface;

	public function __construct( QueryInterface $queryInterface ) {
		$this->queryInterface = $queryInterface;
	}

	/**
	 * @param ItemRow $itemRow
	 */
	public function storeItemRow( ItemRow $itemRow ) {
		$this->queryInterface->insert(
			self::ITEMS_TABLE_NAME,
			array(
				'item_id' => $itemRow->getNumericItemId(),
				'item_json' => $itemRow->getItemJson(),

				'page_title' => $itemRow->getPageTitle(),
				'revision_id' => $itemRow->getRevisionId(),
				'revision_time' => $itemRow->getRevisionTime(),
			)
		);
	}

	/**
	 * @param string|int $numericItemId
	 * @return ItemRow|null
	 */
	public function getItemRowByNumericItemId( $numericItemId ) {
		$rows = $this->queryInterface->select(
			self::ITEMS_TABLE_NAME,
			array(
				'item_id',
				'item_json',
				'page_title',
				'revision_id',
				'revision_time'
			),
			array(
				'item_id' => (int)$numericItemId
			)
		);

		$rows = iterator_to_array( $rows );

		if ( count( $rows ) < 1 ) {
			return false;
		}

		$row = reset( $rows );

		return new ItemRow(
			$row['item_id'],
			$row['item_json'],
			$row['page_title'],
			$row['revision_id'],
			$row['revision_time']
		);
	}

}