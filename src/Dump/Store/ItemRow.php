<?php

namespace Wikibase\Dump\Store;

use Wikibase\DataModel\Entity\ItemId;

/**
 * Value object representing a row in the items table.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ItemRow {

	private $itemId;
	private $itemJson;
	private $pageTitle;
	private $revisionId;
	private $revisionTime;

	/**
	 * @param string|int $numericItemId
	 * @param string $itemJson
	 * @param string $pageTitle
	 * @param string|int $revisionId
	 * @param string $revisionTime
	 */
	public function __construct( $numericItemId, $itemJson, $pageTitle, $revisionId, $revisionTime ) {
		$this->itemId = (int)$numericItemId;
		$this->itemJson = $itemJson;
		$this->pageTitle = $pageTitle;
		$this->revisionId = (int)$revisionId;
		$this->revisionTime = $revisionTime;
	}

	/**
	 * @return int
	 */
	public function getNumericItemId() {
		return $this->itemId;
	}

	/**
	 * @return string
	 */
	public function getItemJson() {
		return $this->itemJson;
	}

	/**
	 * @return string
	 */
	public function getPageTitle() {
		return $this->pageTitle;
	}

	/**
	 * @return int
	 */
	public function getRevisionId() {
		return $this->revisionId;
	}

	/**
	 * @return string
	 */
	public function getRevisionTime() {
		return $this->revisionTime;
	}

}