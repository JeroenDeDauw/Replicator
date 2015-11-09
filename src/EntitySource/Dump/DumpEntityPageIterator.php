<?php

namespace Queryr\Replicator\EntitySource\Dump;

use Iterator;
use Queryr\DumpReader\DumpIterator;
use Queryr\DumpReader\Page;
use Queryr\Replicator\Model\EntityPage;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpEntityPageIterator implements Iterator {

	private $dumpIterator;

	public function __construct( DumpIterator $dumpIterator ) {
		$this->dumpIterator = $dumpIterator;
	}

	/**
	 * @return EntityPage|null
	 */
	public function current() {
		$page = $this->dumpIterator->current();

		return $page === null ? null : $this->dumpPageToEntityPage( $page );
	}

	private function dumpPageToEntityPage( Page $page ) {
		return new EntityPage(
			$page->getRevision()->getText(),
			$page->getTitle(),
			$page->getNamespace(),
			$page->getRevision()->getId(),
			$page->getRevision()->getTimeStamp()
		);
	}

	public function next() {
		$this->dumpIterator->next();
	}

	public function key(): int {
		return $this->dumpIterator->key();
	}

	public function valid(): bool {
		return $this->dumpIterator->valid();
	}

	public function rewind() {
		$this->dumpIterator->rewind();
	}

}
