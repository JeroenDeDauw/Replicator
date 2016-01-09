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

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Bz2JsonImportCommand extends ImportCommandBase {

	protected function configure() {
		$this->setName( 'import:bz2' );
		$this->setDescription( 'Imports entities from a bzip2 compressed JSON dump' );

		$this->addArgument(
			'file',
			InputArgument::REQUIRED,
			'Full path of the bz2 JSON dump file'
		);

		$this->addOption(
			'max',
			'm',
			InputOption::VALUE_OPTIONAL,
			'The maximum number of entities to import'
		);
	}

	protected function executeCommand( InputInterface $input, OutputInterface $output ) {
		$importer = new PagesImporterCli(
			$input,
			$output,
			$this->factory,
			function() use ( $output ) {
				$output->writeln( "\n" );
				$output->writeln( "<info>Import process aborted</info>" );
			}
		);

		$dumpReader = ( new JsonDumpFactory() )->newBz2DumpReader( $input->getArgument( 'file' ) );
		$iterator = $this->factory->newJsonEntityPageIterator( $dumpReader );

		if ( is_numeric( $input->getOption( 'max' ) ) ) {
			$iterator = new \LimitIterator( $iterator, 0, (int)$input->getOption( 'max' ) );
		}

		$importer->runImport( $iterator );
	}

}
