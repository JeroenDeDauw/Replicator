<?php

namespace Tests\Queryr\Dump\Reader;

use Queryr\Dump\Reader\Revision;

/**
 * @covers Queryr\Dump\Reader\Revision
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RevisionTest extends \PHPUnit_Framework_TestCase {

	public function testDataIsRetained() {
		$id = 42;
		$model = 'cats';
		$format = 'spam';
		$text = 'foo';
		$timeStamp = 'bar';

		$page = new Revision( $id, $model, $format, $text, $timeStamp );

		$this->assertSame( $id, $page->getId() );
		$this->assertSame( $model, $page->getModel() );
		$this->assertSame( $format, $page->getFormat() );
		$this->assertSame( $text, $page->getText() );
		$this->assertSame( $timeStamp, $page->getTimeStamp() );
	}

	public function testGivenStringId_getterReturnsInteger() {
		$page = new Revision( '42', '', '', '', '' );
		$this->assertSame( 42, $page->getId() );
	}

}