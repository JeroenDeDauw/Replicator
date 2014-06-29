<?php

namespace Queryr\Replicator\Model;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class EntityPage {

	private $entityJson;
	private $title;
	private $namespaceId;
	private $revisionId;
	private $revisionTime;

	/**
	 * @param string $entityJson
	 * @param string $title
	 * @param string|int $namespaceId
	 * @param string|int $revisionId
	 * @param string $revisionTime
	 */
	public function __construct( $entityJson, $title, $namespaceId, $revisionId, $revisionTime ) {
		$this->entityJson = $entityJson;
		$this->title = $title;
		$this->namespaceId = (int)$namespaceId;
		$this->revisionId = (int)$revisionId;
		$this->revisionTime = $revisionTime;
	}

	/**
	 * @return string
	 */
	public function getEntityJson() {
		return $this->entityJson;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return int
	 */
	public function getNamespaceId() {
		return $this->namespaceId;
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
