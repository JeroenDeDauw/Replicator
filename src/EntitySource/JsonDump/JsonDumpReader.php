<?php

namespace Queryr\Replicator\EntitySource\JsonDump;

use Deserializers\Deserializer;
use Deserializers\Exceptions\DeserializationException;
use Queryr\DumpReader\DumpReaderException;
use Wikibase\DataModel\Entity\EntityDocument;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonDumpReader {

	/**
	 * @var string
	 */
	private $dumpFile;

	/**
	 * @var Deserializer
	 */
	private $deserilaizer;

	/**
	 * @var resource
	 */
	private $handle;

	/**
	 * @param string $dumpFilePath
	 * @param Deserializer $entityDeserilaizer
	 */
	public function __construct( $dumpFilePath, Deserializer $entityDeserilaizer ) {
		$this->dumpFile = $dumpFilePath;
		$this->deserilaizer = $entityDeserilaizer;

		$this->initReader();
	}

	private function initReader() {
		$this->handle = fopen( $this->dumpFile, 'r' );
	}

	public function __destruct() {
		$this->closeReader();
	}

	private function closeReader() {
		fclose( $this->handle );
	}

	public function rewind() {
		$this->closeReader();
		$this->initReader();
	}

	/**
	 * @return EntityDocument|null
	 * @throws DumpReaderException
	 */
	public function nextEntity() {
		do {
			$line = fgets( $this->handle );

			if ( $line === false ) {
				return null;
			}

			if ( $line !== "[\n" ) {
				$data = json_decode( rtrim( $line, ",\n\r" ), true );

				if ( $data !== null ) {
					try {
						return $this->deserilaizer->deserialize( $data );
					}
					catch ( DeserializationException $ex ) {}
				}
			}
		} while ( true );

		return null;
	}

	/**
	 * @return int
	 */
	public function getPosition() {
		if ( PHP_INT_SIZE < 8 ) {
			throw new \RuntimeException( 'Cannot reliably get the file position on 32bit PHP' );
		}

		return ftell( $this->handle );
	}

	/**
	 * @param int $position
	 */
	public function seekToPosition( $position ) {
		fseek( $this->handle, $position );
	}

}
