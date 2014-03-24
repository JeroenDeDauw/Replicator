<?php

namespace Wikibase\DumpReader;

use Wikibase\DumpReader\XmlReader\DumpXmlReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Factory {

	/**
	 * @var string $filePath
	 * @return DumpReader
	 */
	public function newDumpReaderForFile( $filePath ) {
		return new DumpXmlReader( $filePath );
	}

}
