<?php

namespace Tests\Wikibase\Dump;

use Wikibase\Dump\Revision;

/**
 * @covers Wikibase\Dump\Revision
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RevisionTest extends \PHPUnit_Framework_TestCase {

	public function testDataIsRetained() {
		$id = '42';
		$model = 'cats';
		$format = 'spam';
		$text = 'foo';
		$timeStamp = 'bar';

		$page = new Revision( $id, $model, $format, $text, $timeStamp );

		$this->assertEquals( $id, $page->getId() );
		$this->assertEquals( $model, $page->getModel() );
		$this->assertEquals( $format, $page->getFormat() );
		$this->assertEquals( $text, $page->getText() );
		$this->assertEquals( $timeStamp, $page->getTimeStamp() );
	}

}