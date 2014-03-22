<?php

namespace Wikibase\DumpReader;

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
	 * @var string|null
	 */
	private $current;

	/**
	 * @var int
	 */
	private $key;

	public function __construct( DumpReader $reader ) {
		$this->reader = $reader;
	}

	public function current() {
		return $this->current;
	}

	public function next() {
		$this->current = $this->reader->nextEntityJson();
		$this->key++;
	}

	public function key() {
		return $this->key;
	}

	public function valid() {
		return $this->current !== null;
	}

	public function rewind() {
		$this->reader->rewind();
		$this->key = 0;

		$this->next();
	}

}
