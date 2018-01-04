<?php

namespace Tests\Queryr\Replicator\Cli;

use PHPUnit\Framework\TestCase;
use Queryr\Replicator\Cli\ReplicatorCli;
use Symfony\Component\Console\Application;

/**
 * @covers \Queryr\Replicator\Cli\ReplicatorCli
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ReplicatorCliTest extends TestCase {

	public function testNewApplicationReturnsApplication() {
		$replicator = new ReplicatorCli();

		$this->assertInstanceOf(
			Application::class,
			$replicator->newApplication()
		);
	}

}