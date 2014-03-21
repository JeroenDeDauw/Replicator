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

	public function testCanLoadClass() {
		$this->assertTrue( class_exists( 'Wikibase\DumpReader\DumpReader' ) );
	}

	/**
	 * @var DumpReader
	 */
	private $reader;

	public function setUp() {
		$this->reader = new DumpReader( __DIR__ . '/../../data/one-item.xml' );
	}

	public function testReadEntity() {
		$entityJson = $this->reader->nextEntityJson();

		$this->assertInternalType( 'string', $entityJson );

		$entityArray = json_decode( $entityJson, true );
		$this->assertInternalType( 'array', $entityArray );

		$this->assertArrayHasKey( 'entity', $entityArray );
	}

}