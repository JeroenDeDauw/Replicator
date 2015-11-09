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
	public function __construct( string $entityJson, string $title, $namespaceId, $revisionId, string $revisionTime ) {
		$this->entityJson = $entityJson;
		$this->title = $title;
		$this->namespaceId = (int)$namespaceId;
		$this->revisionId = (int)$revisionId;
		$this->revisionTime = $revisionTime;
	}

	public function getEntityJson(): string {
		return $this->entityJson;
	}

	public function getTitle(): string {
		return $this->title;
	}

	public function getNamespaceId(): int {
		return $this->namespaceId;
	}

	public function getRevisionId(): int {
		return $this->revisionId;
	}

	public function getRevisionTime(): string {
		return $this->revisionTime;
	}

}
