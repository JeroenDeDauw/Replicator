<?php

namespace Tests\Wikibase\DumpReader;

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

}