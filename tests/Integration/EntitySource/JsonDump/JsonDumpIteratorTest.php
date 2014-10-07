<?php

namespace Tests\Queryr\Replicator\EntitySource\JsonDump;

use Iterator;
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

	private function assertFindsEntities( array $expectedIds, Iterator $dumpIterator ) {
		$actualIds = [];

		foreach ( $dumpIterator as $entity ) {
			$actualIds[] = $entity->getId()->getSerialization();
		}

		$this->assertEquals( $expectedIds, $actualIds );
	}

	public function testGivenFileWithNoEntities_noEntitiesAreReturned() {
		$iterator = $this->newIteratorForFile( 'simple/empty.json' );

		$this->assertFindsEntities( [], $iterator );
	}

	public function testGivenFileWithOneEntity_oneEntityIsFound() {
		$iterator = $this->newIteratorForFile( 'simple/one-item.json' );

		$this->assertFindsEntities( [ 'Q1' ], $iterator );
	}

	public function testGivenFileWithFiveEntites_fiveEntityAreFound() {
		$iterator = $this->newIteratorForFile( 'simple/five-entities.json' );

		$this->assertFindsEntities( [ 'Q1', 'Q8', 'P16', 'P19', 'P22' ], $iterator );
	}

	public function testGivenFileWithInvalidEntity_noEntityIsFound() {
		$iterator = $this->newIteratorForFile( 'invalid/invalid-item.json' );
		$this->assertFindsEntities( [], $iterator );
	}

	public function testGivenFileWithInvalidEntities_validEntitiesAreFound() {
		$iterator = $this->newIteratorForFile( 'invalid/3valid-2invalid.json' );
		$this->assertFindsEntities( [ 'Q1', 'P16', 'P22' ], $iterator );
	}

	public function testCanDoMultipleIterations() {
		$iterator = $this->newIteratorForFile( 'simple/five-entities.json' );

		$this->assertFindsEntities( [ 'Q1', 'Q8', 'P16', 'P19', 'P22' ], $iterator );
		$this->assertFindsEntities( [ 'Q1', 'Q8', 'P16', 'P19', 'P22' ], $iterator );
	}

	public function testInitialPosition() {
		$reader = new JsonDumpReader( $this->getFilePath( 'simple/five-entities.json' ) );

		$iterator = new JsonDumpIterator(
			$reader,
			TestEnvironment::newInstance()->getFactory()->newCurrentEntityDeserializer()
		);

		$iterator->next();
		$iterator->next();

		$newIterator = new JsonDumpIterator(
			new JsonDumpReader( $this->getFilePath( 'simple/five-entities.json' ), $reader->getPosition() ),
			TestEnvironment::newInstance()->getFactory()->newCurrentEntityDeserializer()
		);

		$this->assertFindsEntities( [ 'P16', 'P19', 'P22' ], $newIterator );
	}
//
//	public function testRewind() {
//		$iterator = $this->newIteratorForFile( 'simple/one-item.json' );
//
//		$this->assertFindsAnotherEntity( $iterator );
//		$iterator->rewind();
//		$this->assertFindsAnotherEntity( $iterator );
//		$this->assertNull( $iterator->nextJsonLine() );
//	}
//
//	public function testResumeFromPosition() {
//		$iterator = $this->newIteratorForFile( 'simple/five-entities.json' );
//
//		$this->assertFindsEntity( $iterator, new ItemId( 'Q1' ) );
//		$this->assertFindsEntity( $iterator, new ItemId( 'Q8' ) );
//
//		$position = $iterator->getPosition();
//		unset( $iterator );
//
//		$newReader = $this->newIteratorForFile( 'simple/five-entities.json' );
//		$newReader->seekToPosition( $position );
//
//		$this->assertFindsEntity( $newReader, new PropertyId( 'P16' ) );
//	}
//
//	public function testFindsAllEntitiesInBigFile() {
//		$iterator = $this->newIteratorForFile( 'big/1000-entities.json' );
//
//		foreach ( range( 0, 20 ) as $i ) {
//			$this->assertFindsAnotherEntity( $iterator );
//		}
//
//		//$this->assertNull( $iterator->nextEntity() );
//	}

}