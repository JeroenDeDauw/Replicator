<?php

namespace Queryr\Replicator\Cli\Command;

use Queryr\DumpReader\DumpReader;
use Queryr\DumpReader\ReaderFactory;
use Queryr\Replicator\Cli\Import\PagesImporterCli;
use Queryr\Replicator\EntitySource\Dump\DumpEntityPageIterator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class XmlDumpImportCommand extends ImportCommandBase {

	protected function configure() {
		$this->setName( 'import:xml' );
		$this->setDescription( 'Imports entities from an extracted XML dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the XML dump'
		);

		$this->addOption(
			'continue',
			'c',
			InputOption::VALUE_OPTIONAL,
			'The title to resume from (title not included)'
		);
	}

	protected function executeCommand( InputInterface $input, OutputInterface $output ) {
		$onAborted = function( $pageTitle ) use ( $output ) {
			$output->writeln( "\n" );
			$output->writeln( "<info>Import process aborted</info>" );
			$output->writeln( "<comment>To resume, run with</comment> --continue=$pageTitle" );
		};

		$importer = new PagesImporterCli( $input, $output, $this->factory, $onAborted );

		$entityPageIterator = new DumpEntityPageIterator( $this->getDumpIterator( $input, $output ) );
		$importer->runImport( $entityPageIterator );
	}

	private function getDumpIterator( InputInterface $input, OutputInterface $output ) {
		$reader = $this->newDumpReader( $input->getArgument( 'file' ) );

		$this->handleContinueArgument( $input, $output, $reader );

		return $reader->getIterator();
	}

	private function handleContinueArgument( InputInterface $input, OutputInterface $output, DumpReader $reader ) {
		$continueTitle = $input->getOption( 'continue' );

		if ( $continueTitle !== null ) {
			$output->write( "<info>Seeking to title </info><comment>$continueTitle</comment><info>... </info>" );
			$reader->seekToTitle( $continueTitle );
			$output->writeln( "<info>done</info>" );
		}
	}

	private function newDumpReader( $file ) {
		$dumpReaderFactory = new ReaderFactory();
		return $dumpReaderFactory->newDumpReaderForFile( $file );
	}

}
