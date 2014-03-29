<?php

namespace Tests\QueryR\Replicator\Commands;

use QueryR\Replicator\Commands\ImportCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers QueryR\Replicator\Commands\ImportCommand
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportCommandTest extends \PHPUnit_Framework_TestCase {

	public function testEntityIdInOutput() {
		$commandTester = new CommandTester( new ImportCommand() );

		$commandTester->execute( array(
			'file' => 'tests/data/simple/one-item.xml'
		) );

		$this->assertRegExp( '/Q15831780/', $commandTester->getDisplay() );
	}

}