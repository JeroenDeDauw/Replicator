<?php

namespace Queryr\Replicator\Cli\Command;

use Queryr\Replicator\Cli\Import\PagesImporterCli;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wikibase\JsonDumpReader\JsonDumpFactory;
use Wikibase\JsonDumpReader\SeekableDumpReader;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GzJsonImportCommand extends ImportCommandBase {

	protected function configure() {
		$this->setName( 'import:gz' );
		$this->setDescription( 'Imports entities from a gzip compressed JSON dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the gz JSON dump file'
		);

		$this->addOption(
			'continue',
			'c',
			InputOption::VALUE_OPTIONAL,
			'The position to resume import from'
		);

		$this->addOption(
			'max',
			'm',
			InputOption::VALUE_OPTIONAL,
			'The maximum number of entities to import'
		);
	}

	protected function executeCommand( InputInterface $input, OutputInterface $output ) {
		$dumpReader = ( new JsonDumpFactory() )->newGzDumpReader(
			$input->getArgument( 'file' ),
			is_numeric( $input->getOption( 'continue' ) ) ? (int)$input->getOption( 'continue' ) : 0
		);

		$importer = new PagesImporterCli(
			$input,
			$output,
			$this->factory,
			function() use ( $output, $dumpReader ) {
				$output->writeln( "\n" );
				$output->writeln( "<info>Import process aborted</info>" );
				$output->writeln( "<comment>To resume, run with</comment> --continue=" . $dumpReader->getPosition() );
			}
		);

		$iterator = $this->factory->newJsonEntityPageIterator( $dumpReader );

		if ( is_numeric( $input->getOption( 'max' ) ) ) {
			$iterator = new \LimitIterator( $iterator, 0, (int)$input->getOption( 'max' ) );
		}

		$importer->runImport( $iterator );

		$this->outputMaxContinuation( $input, $output, $dumpReader );
	}

	private function outputMaxContinuation( InputInterface $input, OutputInterface $output, SeekableDumpReader $reader ) {
		if ( is_numeric( $input->getOption( 'max' ) ) ) {
			$output->writeln(
				"\n<comment>To continue from current position, run with</comment> --continue=" . $reader->getPosition()
			);
		}
	}

}
