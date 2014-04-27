<?php

namespace Queryr\Replicator\Commands\Installer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
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