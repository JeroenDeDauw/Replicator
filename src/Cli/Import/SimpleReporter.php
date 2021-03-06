<?php

namespace Queryr\Replicator\Cli\Import;

use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Model\EntityPage;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SimpleReporter implements PageImportReporter {

	private $output;

	private $number = 0;

	public function __construct( OutputInterface $output ) {
		$this->output = $output;
	}

	public function started( EntityPage $entityPage ) {
		$this->output->write(
			"\n<info>Importing entity " . ++$this->number . ': ' . $entityPage->getTitle() . '...</info>'
		);
	}

	public function endedSuccessfully() {
		$this->output->writeln( '<info> Entity imported.</info>' );
	}

	public function endedWithError( \Exception $ex ) {
		$this->output->writeln( '<error>FAILED!</error>' );
		$this->output->writeln( '<error>Error details: ' . $ex->getMessage() . '</error>' );
	}

	public function stepStarted( string $message ) {
	}

	public function stepCompleted() {
		$this->output->write( '<info>..</info>' );
	}

}