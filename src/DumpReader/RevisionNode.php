<?php

namespace Wikibase\DumpReader;

use DOMNode;
use XMLReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RevisionNode {

	private $isEntity = false;
	private $text;

	public function __construct( DOMNode $pageNode ) {
		foreach ( $pageNode->childNodes as $childNode ) {
			$this->handleNode( $childNode );
		}
	}

	private function handleNode( DOMNode $node ) {
		if ( $this->isModelNode( $node ) ) {
			$this->isEntity = $this->isEntityModel( $node );
		}

		if ( $this->isTextNode( $node ) ) {
			$this->text = $node->textContent;
		}
	}

	private function isModelNode( DOMNode $node ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === 'model';
	}

	private function isEntityModel( DOMNode $node ) {
		return $node->textContent === 'wikibase-item' || $node->textContent === 'wikibase-property';
	}

	private function isTextNode( DOMNode $node ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === 'text';
	}

	public function isEntity() {
		return $this->isEntity;
	}

	public function getEntityJson() {
		return $this->text;
	}

}