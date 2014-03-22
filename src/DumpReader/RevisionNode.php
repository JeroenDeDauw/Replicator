<?php

namespace Wikibase\DumpReader;

use DOMNode;
use XMLReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RevisionNode {

	private $isItem;
	private $text;

	public function __construct( DOMNode $pageNode ) {
		$isItem = false;

		foreach ( $pageNode->childNodes as $childNode ) {
			if ( $this->isModelNode( $childNode ) ) {
				$isItem = $this->isItemModel( $childNode );
			}

			if ( $this->isTextNode( $childNode ) ) {
				$this->extractJson( $childNode );
			}
		}

		$this->isItem = $isItem;
	}

	public function isItem() {
		return $this->isItem;
	}

	private function isModelNode( DOMNode $node ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === 'model';
	}

	private function isItemModel( DOMNode $node ) {
		return $node->textContent === 'wikibase-item';
	}

	private function isTextNode( DOMNode $node ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === 'text';
	}

	private function extractJson( DOMNode $node ) {
		$this->text = $node->textContent;
	}

	public function getItemJson() {
		return $this->text;
	}

}