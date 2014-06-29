<?php

namespace Tests\Queryr\DumpReader;

use Queryr\DumpReader\DumpIterator;
use Queryr\DumpReader\XmlReader\DumpXmlReader;

/**
 * @covers Queryr\DumpReader\DumpIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DumpIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testIterationWorks() {
		$iterator = new DumpIterator( new DumpXmlReader( $this->getFilePath( 'simple/two-items.xml' ) ) );

		$this->assertCount( 2, iterator_to_array( $iterator ) );
		$this->assertContainsOnlyInstancesOf( 'Queryr\DumpReader\Page', $iterator );
	}

	private function getFilePath( $fileName ) {
		return __DIR__ . '/../../../data/' . $fileName;
	}

}