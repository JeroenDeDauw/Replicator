<?php

namespace Queryr\Replicator\Commands;

use Symfony\Component\Console\Command\Command;

class RunTestsCommand extends Command {

	protected function configure() {
		$this->setName( 'run-tests' );
	}

}