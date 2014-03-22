<?php

namespace Tests\Wikibase\DumpReader;

use Wikibase\DumpReader\DumpReader;

/**
 * @covers Wikibase\DumpReader\DumpReader
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpReaderTest extends \PHPUnit_Framework_TestCase {

	public function testGivenFileWithNoEntities_nullIsReturned() {
		$reader = $this->newReaderForFile( 'empty.xml' );

		$this->assertNull( $reader->nextEntityJson() );
	}

	private function newReaderForFile( $fileName ) {
		return new DumpReader( $this->getFilePath( $fileName ) );
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../data/simple/' . $fileName;
	}

	private function assertFindsAnotherEntity( DumpReader $reader ) {
		$entityJson = $reader->nextEntityJson();
		$this->assertIsEntityJson( $entityJson );
	}

	private function assertIsEntityJson( $entityJson ) {
		$this->assertInternalType( 'string', $entityJson );

		$entityArray = json_decode( $entityJson, true );
		$this->assertInternalType( 'array', $entityArray );

		$this->assertArrayHasKey( 'entity', $entityArray );
	}

	public function testGivenFileWithOneEntity_oneEntityIsFound() {
		$reader = $this->newReaderForFile( 'one-item.xml' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntityJson() );
	}

	public function testGivenFileWithTwoEntities_twoEntitiesAreFound() {
		$reader = $this->newReaderForFile( 'two-items.xml' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntityJson() );
	}

}