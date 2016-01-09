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
class JsonDumpImportCommand extends ImportCommandBase {

	protected function configure() {
		$this->setName( 'import:json' );
		$this->setDescription( 'Imports entities from an extracted JSON dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the JSON dump file'
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
		$reader = ( new JsonDumpFactory() )->newExtractedDumpReader(
			$input->getArgument( 'file' ),
			$input->getOption( 'continue' ) === null ? 0 : (int)$input->getOption( 'continue' )
		);

		$onAborted = function() use ( $output, $reader ) {
			$output->writeln( "\n" );
			$output->writeln( "<info>Import process aborted</info>" );
			$output->writeln( "<comment>To resume, run with</comment> --continue=" . $reader->getPosition() );
		};

		$importer = new PagesImporterCli( $input, $output, $this->factory, $onAborted );

		$iterator = $this->factory->newJsonEntityPageIterator( $reader );

		if ( is_numeric( $input->getOption( 'max' ) ) ) {
			$iterator = new \LimitIterator( $iterator, 0, (int)$input->getOption( 'max' ) );
		}

		$importer->runImport( $iterator );

		$this->outputMaxContinuation( $input, $output, $reader );
	}

	private function outputMaxContinuation( InputInterface $input, OutputInterface $output, SeekableDumpReader $reader ) {
		if ( is_numeric( $input->getOption( 'max' ) ) ) {
			$output->writeln(
				"\n<comment>To continue from current position, run with</comment> --continue=" . $reader->getPosition()
			);
		}
	}

}
