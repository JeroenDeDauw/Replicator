<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\DumpReader\Page;
use Queryr\Replicator\Importer\PageImportReporter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class VerboseReporter implements PageImportReporter {

	private $output;

	private $number = 0;

	public function __construct( OutputInterface $output ) {
		$this->output = $output;
	}

	public function started( Page $entityPage ) {
		$this->output->writeln(
			"\n<info>Importing entity " . ++$this->number . ': ' . $entityPage->getTitle() . '...</info>'
		);
	}

	public function endedSuccessfully() {
		$this->output->writeln( "<info>\t Entity imported.</info>" );
	}

	public function endedWithError( \Exception $ex ) {
		$this->output->writeln( "<error>FAILED!</error>" );
		$this->output->writeln( "\t <error>Error details: " . $ex->getMessage() . '</error>' );
	}

	public function stepStarted( $message ) {
		$this->output->write( "<comment>\t* $message... </comment>" );
	}

	public function stepCompleted() {
		$this->output->writeln( "<comment>done.</comment>" );
	}

}