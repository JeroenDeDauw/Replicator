<?php

namespace Wikibase\DumpReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Page {

	private $id;
	private $title;
	private $namespace;
	private $revision;

	public function __construct( $id, $title, $namespace, Revision $revision ) {
		$this->id = $id;
		$this->title = $title;
		$this->namespace = $namespace;
		$this->revision = $revision;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
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

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

}
