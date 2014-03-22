<?php

namespace Tests\Wikibase\DumpReader;

use Wikibase\DumpReader\DumpReader;
use Wikibase\DumpReader\DumpIterator;

/**
 * @covers Wikibase\DumpReader\DumpIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testGivenFileWithTwoEntities_twoEntitiesAreFound() {
		$iterator = new DumpIterator( new DumpReader( $this->getFilePath( 'simple/two-items.xml' ) ) );

		$this->assertCount( 2, $iterator );
		$this->assertContainsOnly( 'string', $iterator );
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../data/' . $fileName;
	}

}