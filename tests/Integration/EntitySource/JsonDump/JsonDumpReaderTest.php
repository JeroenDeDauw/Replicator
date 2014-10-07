<?php

namespace Tests\Queryr\Replicator\EntitySource\JsonDump;

use Queryr\Replicator\EntitySource\JsonDump\JsonDumpReader;

/**
 * @covers Queryr\Replicator\EntitySource\JsonDump\JsonDumpReader
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonDumpReaderTest extends \PHPUnit_Framework_TestCase {

	private function newReaderForFile( $fileName ) {
		return new JsonDumpReader(
			$this->getFilePath( $fileName )
		);
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../../data/' . $fileName;
	}

	private function assertFindsAnotherJsonLine( JsonDumpReader $reader ) {
		$this->assertJson( $reader->nextJsonLine() );
	}

	private function assertFindsEntity( JsonDumpReader $reader, $expectedId ) {
		$line = $reader->nextJsonLine();
		$this->assertJson( $line );
		$this->assertContains( $expectedId, $line );
	}

	public function testGivenFileWithNoEntities_nullIsReturned() {
		$reader = $this->newReaderForFile( 'simple/empty.json' );

		$this->assertNull( $reader->nextJsonLine() );
	}

	public function testGivenFileWithOneEntity_oneEntityIsFound() {
		$reader = $this->newReaderForFile( 'simple/one-item.json' );

		$this->assertFindsAnotherJsonLine( $reader );
		$this->assertNull( $reader->nextJsonLine() );
	}

	public function testGivenFileWithFiveEntites_fiveEntityAreFound() {
		$reader = $this->newReaderForFile( 'simple/five-entities.json' );

		$this->assertFindsAnotherJsonLine( $reader );
		$this->assertFindsAnotherJsonLine( $reader );
		$this->assertFindsAnotherJsonLine( $reader );
		$this->assertFindsAnotherJsonLine( $reader );
		$this->assertFindsAnotherJsonLine( $reader );
		$this->assertNull( $reader->nextJsonLine() );
	}

	public function testRewind() {
		$reader = $this->newReaderForFile( 'simple/one-item.json' );

		$this->assertFindsAnotherJsonLine( $reader );
		$reader->rewind();
		$this->assertFindsAnotherJsonLine( $reader );
		$this->assertNull( $reader->nextJsonLine() );
	}

	public function testResumeFromPosition() {
		$reader = $this->newReaderForFile( 'simple/five-entities.json' );

		$this->assertFindsEntity( $reader, 'Q1' );
		$this->assertFindsEntity( $reader, 'Q8' );

		$position = $reader->getPosition();
		unset( $reader );

		$newReader = $this->newReaderForFile( 'simple/five-entities.json' );
		$newReader->seekToPosition( $position );

		$this->assertFindsEntity( $newReader, 'P16' );
	}

	public function testFindsAllEntitiesInBigFile() {
		$reader = $this->newReaderForFile( 'big/1000-entities.json' );

		foreach ( range( 0, 20 ) as $i ) {
			$this->assertFindsAnotherJsonLine( $reader );
		}

		//$this->assertNull( $reader->nextEntity() );
	}

}