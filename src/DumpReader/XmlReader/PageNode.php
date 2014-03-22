<?php

namespace Wikibase\DumpReader\XmlReader;

use DOMNode;
use Wikibase\DumpReader\DumpReaderException;
use XMLReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageNode {

	private $DOMNode;

	public function __construct( DOMNode $pageNode ) {
		$this->DOMNode = $pageNode;
	}

	public function getRevisionNode() {
		foreach ( $this->DOMNode->childNodes as $childNode ) {

			if ( $this->isRevisionNode( $childNode ) ) {
				return new RevisionNode( $childNode );
			}
		}

		throw new DumpReaderException( 'No revision node found' );
	}

	private function isRevisionNode( DOMNode $node ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === 'revision';
	}

}
