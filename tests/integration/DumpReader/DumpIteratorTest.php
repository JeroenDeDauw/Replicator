<?php

namespace Tests\Wikibase\DumpReader;

use Wikibase\DumpReader\DumpIterator;
use Wikibase\DumpReader\XmlReader\DumpXmlReader;

/**
 * @covers Wikibase\DumpReader\DumpIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testIterationWorks() {
		$iterator = new DumpIterator( new DumpXmlReader( $this->getFilePath( 'simple/two-items.xml' ) ) );

		$this->assertCount( 2, iterator_to_array( $iterator ) );
		$this->assertContainsOnlyInstancesOf( 'Wikibase\DumpReader\Page', $iterator );
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../data/' . $fileName;
	}

}