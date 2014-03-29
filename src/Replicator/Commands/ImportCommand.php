<?php

namespace QueryR\Replicator\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\Dump\Reader\Page;
use Wikibase\Dump\Reader\ReaderFactory;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ImportCommand extends Command {

	protected function configure() {
		$this->setName( 'import' );
		$this->setDescription( 'Imports entities from an XML dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the XML dump'
		);
	}

	protected function execute( InputInterface $input, OutputInterface $output ) {
		$executor = new ImportCommandExecutor( $input, $output );
		$executor->run();
	}

}

class ImportCommandExecutor {

	private $input;
	private $output;

	public function __construct( InputInterface $input, OutputInterface $output ) {
		$this->input = $input;
		$this->output = $output;
	}

	public function run() {
		$dumpReader = $this->newDumpReader();

		/**
		 * @var Page $entityPage
		 */
		foreach ( $dumpReader->getIterator() as $key => $entityPage ) {
			$this->importEntityPage( $key, $entityPage );
		}
	}

	private function newDumpReader() {
		$dumpReaderFactory = new ReaderFactory();

		return $dumpReaderFactory->newDumpReaderForFile( $this->input->getArgument( 'file' ) );
	}

	private function importEntityPage( $importNumber, Page $entityPage ) {
		$this->output->writeln( 'Importing entity ' . $importNumber . ': ' . $entityPage->getTitle() );
	}

}