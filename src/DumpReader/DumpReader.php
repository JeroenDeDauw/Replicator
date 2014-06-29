<?php

namespace Queryr\DumpReader;

use Iterator;
use IteratorAggregate;

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
	 * Seeks to the Page with the provided title and returns it.
	 * If no page is found, null is returned.
	 *
	 * @param string $titleToSeekTo
	 *
	 * @return Page|null
	 */
	public function seekToTitle( $titleToSeekTo ) {
		while ( $page = $this->nextEntityPage() ) {
			if ( $page->getTitle() === $titleToSeekTo ) {
				return $page;
			}
		}

		return null;
	}

	/**
	 * Returns an Iterator for the REMAINING pages.
	 * Caution: the iterator affects the position of the dump reader itself.
	 *
	 * @see IteratorAggregate::getIterator
	 * @return Iterator
	 */
	public function getIterator() {
		return new DumpIterator( $this );
	}

}
