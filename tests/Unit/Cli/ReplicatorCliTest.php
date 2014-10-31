<?php

namespace Tests\Queryr\Replicator\Cli;

use Queryr\Replicator\Cli\ReplicatorCli;

/**
 * @covers Queryr\Replicator\Cli\ReplicatorCli
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReplicatorCliTest extends \PHPUnit_Framework_TestCase {

	public function testNewApplicationReturnsApplication() {
		$replicator = new ReplicatorCli();

		$this->assertInstanceOf(
			'Symfony\Component\Console\Application',
			$replicator->newApplication()
		);
	}

}