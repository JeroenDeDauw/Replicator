<?php

namespace Wikibase\DumpReader;

use DOMNode;
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

		return $revisionNode->getItemJson();
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

		throw new \RuntimeException( 'No revision node found' );
	}

	private function isRevisionNode( DOMNode $node ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === 'revision';
	}

}

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