<?php

namespace QueryR\Replicator\Commands\Installer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait ProgressTrait {

	/**
	 * @var InputInterface
	 */
	private $input;

	/**
	 * @var OutputInterface
	 */
	private $output;

	private function writeProgress( $message ) {
		$this->output->write( "<comment>$message... </comment>" );
	}

	private function writeProgressEnd( $message = 'done.' ) {
		$this->output->writeln( "<comment>$message</comment>" );
	}

	private function writeError( $message ) {
		$this->output->writeln( "<error>$message</error>" );
	}

}