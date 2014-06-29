<?php

namespace Queryr\DumpReader;

use Iterator;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpIterator implements Iterator {

	/**
	 * @var DumpReader
	 */
	private $reader;

	/**
	 * @var Page|null
	 */
	private $current;

	/**
	 * @var int
	 */
	private $key = 0;

	public function __construct( DumpReader $reader ) {
		$this->reader = $reader;
	}

	/**
	 * @return Page|null
	 */
	public function current() {
		return $this->current;
	}

	public function next() {
		$this->current = $this->reader->nextEntityPage();
		$this->key++;
	}

	/**
	 * @return int
	 */
	public function key() {
		return $this->key;
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return $this->current !== null;
	}

	public function rewind() {
		$this->key = 0;
		$this->next();
	}

}
