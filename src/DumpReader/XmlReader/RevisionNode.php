<?php

namespace Queryr\DumpReader\XmlReader;

use DOMNode;
use Queryr\DumpReader\Revision;
use XMLReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RevisionNode {

	private $id;
	private $model;
	private $format;
	private $text;
	private $timeStamp;

	public function __construct( DOMNode $pageNode ) {
		foreach ( $pageNode->childNodes as $childNode ) {
			$this->handleNode( $childNode );
		}
	}

	private function handleNode( DOMNode $node ) {
		if ( $this->hasElementName( $node, 'id' ) ) {
			$this->id = $node->textContent;
		}

		if ( $this->hasElementName( $node, 'model' ) ) {
			$this->model = $node->textContent;
		}

		if ( $this->hasElementName( $node, 'format' ) ) {
			$this->format = $node->textContent;
		}

		if ( $this->hasElementName( $node, 'text' ) ) {
			$this->text = $node->textContent;
		}

		if ( $this->hasElementName( $node, 'timestamp' ) ) {
			$this->timeStamp = $node->textContent;
		}
	}

	private function hasElementName( DOMNode $node, $name ) {
		return $node->nodeType === XMLReader::ELEMENT && $node->nodeName === $name;
	}

	public function asRevision() {
		return new Revision(
			$this->id,
			$this->model,
			$this->format,
			$this->text,
			$this->timeStamp
		);
	}

}