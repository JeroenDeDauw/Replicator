<?php

namespace Queryr\DumpReader\XmlReader;

use DOMNode;
use Queryr\DumpReader\DumpReaderException;
use Queryr\DumpReader\Page;
use Queryr\DumpReader\Revision;
use XMLReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class PageNode {

	private $id;
	private $title;
	private $namespace;

	/**
	 * @var Revision
	 */
	private $revision;

	public function __construct( DOMNode $pageNode ) {
		$this->handleNodes( $pageNode );
	}

	private function handleNodes( DOMNode $pageNode ) {
		foreach ( $pageNode->childNodes as $childNode ) {
			$this->handleNode( $childNode );
		}
	}

	private function handleNode( DOMNode $node ) {
		if ( $this->hasElementName( $node, 'id' ) ) {
			$this->id = $node->textContent;
		}

		if ( $this->hasElementName( $node, 'title' ) ) {
			$this->title = $node->textContent;
		}

		if ( $this->hasElementName( $node, 'ns' ) ) {
			$this->namespace = $node->textContent;
		}

		if ( $this->hasElementName( $node, 'revision' ) ) {
			$node = new RevisionNode( $node );
			$this->revision = $node->asRevision();
		}
	}

	private function hasElementName( DOMNode $node, $name ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === $name;
	}

	public function asPage() {
		if ( !$this->revision ) {
			throw new DumpReaderException( 'No revision node found' );
		}

		return new Page(
			$this->id,
			$this->title,
			$this->namespace,
			$this->revision
		);
	}

}
