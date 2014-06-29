<?php

namespace Queryr\DumpReader\XmlReader;

use Queryr\DumpReader\DumpReader;
use Queryr\DumpReader\DumpReaderException;
use Queryr\DumpReader\Page;
use XMLReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpXmlReader extends DumpReader {

	/**
	 * @var XMLReader
	 */
	private $xmlReader;

	/**
	 * @var string
	 */
	private $dumpFile;

	public function __construct( $dumpFile ) {
		$this->dumpFile = $dumpFile;

		$this->initReader();
	}

	private function initReader() {
		$this->xmlReader = new XMLReader();
		$this->xmlReader->open( $this->dumpFile );
	}

	public function __destruct() {
		$this->closeReader();
	}

	private function closeReader() {
		$this->xmlReader->close();
	}

	/**
	 * @see DumpReader::rewind
	 */
	public function rewind() {
		$this->closeReader();
		$this->initReader();
	}

	/**
	 * @see DumpReader::nextEntityPage
	 *
	 * @return Page|null
	 * @throws DumpReaderException
	 */
	public function nextEntityPage() {
		do {
			$page = $this->nextPage();

			if ( $page === null ) {
				return null;
			}
		} while ( !$page->getRevision()->hasEntityModel() );

		return $page;
	}

	/**
	 * @return Page|null
	 */
	private function nextPage() {
		while ( !$this->isPageNode() ) {
			$this->xmlReader->read();

			if ( $this->xmlReader->nodeType === XMLReader::NONE ) {
				return null;
			}
		}

		$pageNode = new PageNode( $this->xmlReader->expand() );

		$page = $pageNode->asPage();
		$this->xmlReader->next();
		return $page;
	}

	private function isPageNode() {
		return $this->xmlReader->nodeType === XMLReader::ELEMENT && $this->xmlReader->name === 'page';
	}

}
