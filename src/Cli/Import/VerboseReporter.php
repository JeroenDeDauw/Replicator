<?php

namespace Queryr\Replicator\Cli\Import;

use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Model\EntityPage;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class VerboseReporter implements PageImportReporter {

	private $output;
	private $veryVerbose;

	private $number = 0;
	private $stepStartTime;

	public function __construct( OutputInterface $output, $veryVerbose = false ) {
		$this->output = $output;
		$this->veryVerbose = $veryVerbose;
	}

	public function started( EntityPage $entityPage ) {
		$this->output->writeln(
			"\n<info>Importing entity " . ++$this->number . ': ' . $entityPage->getTitle() . '...</info>'
		);
	}

	public function endedSuccessfully() {
		$this->output->writeln( "<info>\t Entity imported.</info>" );
	}

	public function endedWithError( \Exception $ex ) {
		$this->output->writeln( '<error>FAILED!</error>' );
		$this->output->writeln( "\t <error>Error details: " . $ex->getMessage() . '</error>' );
	}

	public function stepStarted( string $message ) {
		$this->stepStartTime = microtime( true );
		$this->output->write( "<comment>\t* $message... </comment>" );
	}

	public function stepCompleted() {
		if ( $this->veryVerbose ) {
			$ms = round( ( microtime( true ) - $this->stepStartTime ) * 1000, 2 );
			$details = " ($ms ms)";
		}
		else {
			$details = '';
		}

		$this->output->writeln( "<comment>done$details.</comment>" );
	}

}