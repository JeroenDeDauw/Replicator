<?php

namespace Queryr\DumpReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Page {

	private $id;
	private $title;
	private $namespace;
	private $revision;

	/**
	 * @param string|int $id
	 * @param string $title
	 * @param string|int $namespace
	 * @param Revision $revision
	 */
	public function __construct( $id, $title, $namespace, Revision $revision ) {
		$this->id = (int)$id;
		$this->title = $title;
		$this->namespace = (int)$namespace;
		$this->revision = $revision;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
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
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 * @return Revision
	 */
	public function getRevision() {
		return $this->revision;
	}

}
