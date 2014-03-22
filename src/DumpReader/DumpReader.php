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

	public function nextEntityJson() {
		$revisionNode = $this->nextRevisionNode();

		while ( !$revisionNode->isItem() ) {
			$revisionNode = $this->nextRevisionNode();
		}

		$json = $revisionNode->getItemJson();
		$this->xmlReader->next();
		return $json;
	}

	private function nextRevisionNode() {
		while ( !$this->isPageNode() ) {
			$this->xmlReader->read();
		}

		$pageNode = new PageNode( $this->xmlReader->expand() );
		return $pageNode->getRevisionNode();
	}

	private function isPageNode() {
		return $this->xmlReader->nodeType === XMLReader::ELEMENT && $this->xmlReader->name === 'page';
	}

}
