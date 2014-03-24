<?php

namespace Tests\QueryR\Replicator;

use QueryR\Replicator\Replicator;

/**
 * @covers QueryR\Replicator\Replicator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReplicatorTest extends \PHPUnit_Framework_TestCase {

	public function testNewApplicationReturnsApplication() {
		$replicator = new Replicator();

		$this->assertInstanceOf(
			'Symfony\Component\Console\Application',
			$replicator->newApplication()
		);
	}

}