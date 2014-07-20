<?php

namespace Tests\Queryr\Replicator\EntitySource;

use Queryr\Replicator\EntitySource\BatchingEntityPageFetcher;
use Queryr\Replicator\EntitySource\ReferencedEntityPageIterator;
use Queryr\Replicator\Model\EntityPage;

/**
 * @covers Queryr\Replicator\EntitySource\ReferencedEntityPageIterator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReferencedEntityPageIteratorTest extends \PHPUnit_Framework_TestCase {

	public function testCanIterateOverEntityPagesWithNoClaims() {
		//$iterator = new ReferencedEntityPageIterator();
		// TODO

		$this->assertTrue( true );
	}

}
