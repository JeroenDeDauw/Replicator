<?php

namespace QueryR\Replicator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReplicateCommand extends Command {

	protected function configure() {
		$this->setName( 'replicate' );
		$this->setDescription( 'Fetches the latest XML dump and imports it' );
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		// TODO
	}

}