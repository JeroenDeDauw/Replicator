<?php

namespace Tests\Queryr\Replicator\EntitySource\JsonDump;

use Queryr\DumpReader\Revision;
use Queryr\Replicator\EntitySource\JsonDump\JsonDumpReader;
use Tests\Queryr\Replicator\Integration\TestEnvironment;

/**
 * @covers Queryr\Replicator\EntitySource\JsonDump\JsonDumpReader
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class JsonDumpReaderTest extends \PHPUnit_Framework_TestCase {

	private function newReaderForFile( $fileName ) {
		return new JsonDumpReader(
			$this->getFilePath( $fileName ),
			TestEnvironment::newInstance()->getFactory()->newCurrentEntityDeserializer()
		);
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../../data/' . $fileName;
	}

	private function assertFindsAnotherEntity( JsonDumpReader $reader ) {
		$entity = $reader->nextEntity();
		$this->assertInstanceOf( 'Wikibase\DataModel\Entity\EntityDocument', $entity );
	}

	private function assertHasEntityJson( Revision $revision ) {
		$entityArray = json_decode( $revision->getText(), true );
		$this->assertInternalType( 'array', $entityArray );

		$this->assertArrayHasKey( 'entity', $entityArray );
	}

	public function testGivenFileWithNoEntities_nullIsReturned() {
		$reader = $this->newReaderForFile( 'simple/empty.json' );

		$this->assertNull( $reader->nextEntity() );
	}

	public function testGivenFileWithOneEntity_oneEntityIsFound() {
		$reader = $this->newReaderForFile( 'simple/one-item.json' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntity() );
	}

	public function testGivenFileWithFiveEntites_fiveEntityAreFound() {
		$reader = $this->newReaderForFile( 'simple/five-entities.json' );

		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntity() );
	}

	public function testGivenFileWithInvalidEntity_noEntityIsFound() {
		$reader = $this->newReaderForFile( 'invalid/invalid-item.json' );
		$this->assertNull( $reader->nextEntity() );
	}

	public function testGivenFileWithInvalidEntities_validEntitiesAreFound() {
		$reader = $this->newReaderForFile( 'invalid/3valid-2invalid.json' );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertFindsAnotherEntity( $reader );
		$this->assertNull( $reader->nextEntity() );
	}

}