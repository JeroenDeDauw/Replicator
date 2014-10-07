<?php

namespace Tests\Queryr\Replicator\EntitySource\JsonDump;

use Queryr\Replicator\EntitySource\JsonDump\JsonDumpIterator;
use Queryr\Replicator\EntitySource\JsonDump\JsonDumpReader;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @covers Queryr\Replicator\EntitySource\JsonDump\JsonDumpIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonDumpIteratorTest extends \PHPUnit_Framework_TestCase {

	private function newIteratorForFile( $fileName ) {
		return new JsonDumpIterator(
			new JsonDumpReader( $this->getFilePath( $fileName ) ),
			TestEnvironment::newInstance()->getFactory()->newCurrentEntityDeserializer()
		);
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../../data/' . $fileName;
	}

	private function assertFindsEntities( array $expectedIds, JsonDumpIterator $dumpIterator ) {
		$actualIds = [];

		foreach ( $dumpIterator as $entity ) {
			$actualIds[] = $entity->getId()->getSerialization();
		}

		$this->assertEquals( $expectedIds, $actualIds );
	}

	public function testGivenFileWithNoEntities_noEntitiesAreReturned() {
		$reader = $this->newIteratorForFile( 'simple/empty.json' );

		$this->assertFindsEntities( [], $reader );
	}

	public function testGivenFileWithOneEntity_oneEntityIsFound() {
		$reader = $this->newIteratorForFile( 'simple/one-item.json' );

		$this->assertFindsEntities( [ 'Q1' ], $reader );
	}



	public function testGivenFileWithFiveEntites_fiveEntityAreFound() {
		$reader = $this->newIteratorForFile( 'simple/five-entities.json' );

		$this->assertFindsEntities( [ 'Q1', 'Q8', 'P16', 'P19', 'P22' ], $reader );
	}

	public function testGivenFileWithInvalidEntity_noEntityIsFound() {
		$reader = $this->newIteratorForFile( 'invalid/invalid-item.json' );
		$this->assertFindsEntities( [], $reader );
	}

	public function testGivenFileWithInvalidEntities_validEntitiesAreFound() {
		$reader = $this->newIteratorForFile( 'invalid/3valid-2invalid.json' );
		$this->assertFindsEntities( [ 'Q1', 'P16', 'P22' ], $reader );
	}
//
//	public function testRewind() {
//		$reader = $this->newIteratorForFile( 'simple/one-item.json' );
//
//		$this->assertFindsAnotherEntity( $reader );
//		$reader->rewind();
//		$this->assertFindsAnotherEntity( $reader );
//		$this->assertNull( $reader->nextJsonLine() );
//	}
//
//	public function testResumeFromPosition() {
//		$reader = $this->newIteratorForFile( 'simple/five-entities.json' );
//
//		$this->assertFindsEntity( $reader, new ItemId( 'Q1' ) );
//		$this->assertFindsEntity( $reader, new ItemId( 'Q8' ) );
//
//		$position = $reader->getPosition();
//		unset( $reader );
//
//		$newReader = $this->newIteratorForFile( 'simple/five-entities.json' );
//		$newReader->seekToPosition( $position );
//
//		$this->assertFindsEntity( $newReader, new PropertyId( 'P16' ) );
//	}
//
//	public function testFindsAllEntitiesInBigFile() {
//		$reader = $this->newIteratorForFile( 'big/1000-entities.json' );
//
//		foreach ( range( 0, 20 ) as $i ) {
//			$this->assertFindsAnotherEntity( $reader );
//		}
//
//		//$this->assertNull( $reader->nextEntity() );
//	}

}