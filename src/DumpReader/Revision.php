<?php

namespace Wikibase\DumpReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Revision {

	private $id;
	private $model;
	private $format;
	private $text;
	private $timeStamp;

	public function __construct( $id, $model, $format, $text, $timeStamp ) {
		$this->id = $id;
		$this->model = $model;
		$this->format = $format;
		$this->text = $text;
		$this->timeStamp = $timeStamp;
	}

	/**
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
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
	public function getModel() {
		return $this->model;
	}

	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * @return string
	 */
	public function getTimeStamp() {
		return $this->timeStamp;
	}

	/**
	 * @return bool
	 */
	public function hasEntityModel() {
		return $this->model === 'wikibase-item' || $this->model === 'wikibase-property';
	}

}
