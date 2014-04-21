<?php

namespace QueryR\Dump\Reader;

use QueryR\Dump\Reader\XmlReader\DumpXmlReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReaderFactory {

	/**
	 * @var string $filePath
	 * @return DumpReader
	 */
	public function newDumpReaderForFile( $filePath ) {
		return new DumpXmlReader( $filePath );
	}

}
