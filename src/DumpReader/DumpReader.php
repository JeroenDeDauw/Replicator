<?php

namespace Wikibase\DumpReader;

use XMLReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpReader {

	/**
	 * @var XMLReader
	 */
	private $xmlReader;

	public function __construct( $dumpFile ) {
		$this->xmlReader = new XMLReader();
		$this->xmlReader->open( $dumpFile );
	}

	public function __destruct() {
		$this->xmlReader->close();
	}

	/**
	 * Returns a string with the json of the next entity,
	 * or null if there are no further entities.
	 *
	 * @return string|null
	 */
	public function nextEntityJson() {
		$revisionNode = $this->nextRevisionNode();

		if ( $revisionNode === null ) {
			return null;
		}

		while ( !$revisionNode->isEntity() ) {
			$revisionNode = $this->nextRevisionNode();

			if ( $revisionNode === null ) {
				return null;
			}
		}

		return $revisionNode->getEntityJson();
	}

	/**
	 * @return RevisionNode|null
	 */
	private function nextRevisionNode() {
		while ( !$this->isPageNode() ) {
			$this->xmlReader->read();

			if ( $this->xmlReader->nodeType === XMLReader::NONE ) {
				return null;
			}
		}

		$pageNode = new PageNode( $this->xmlReader->expand() );

		$revisionNode = $pageNode->getRevisionNode();
		$this->xmlReader->next();
		return $revisionNode;
	}

	private function isPageNode() {
		return $this->xmlReader->nodeType === XMLReader::ELEMENT && $this->xmlReader->name === 'page';
	}

}
