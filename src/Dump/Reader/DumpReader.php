<?php

namespace Wikibase\Dump\Reader;

use Iterator;
use IteratorAggregate;
use Wikibase\Dump\Page;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class DumpReader implements IteratorAggregate {

	/**
	 * Returns a string with the json of the next entity,
	 * or null if there are no further entities.
	 *
	 * @return Page|null
	 * @throws DumpReaderException
	 */
	public abstract function nextEntityPage();

	/**
	 * Rewinds the reader to the beginning of the dump.
	 */
	public abstract function rewind();

	/**
	 * @see IteratorAggregate::getIterator
	 * @return Iterator
	 */
	public function getIterator() {
		return new DumpIterator( $this );
	}

}
