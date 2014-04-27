<?php

namespace Queryr\Replicator\Importer\Console;

use Queryr\Dump\Reader\ReaderFactory;
use Queryr\Replicator\Importer\ImportStats;
use Queryr\Replicator\Importer\PageImporter;
use Queryr\Replicator\Importer\PageImportReporter;
use Queryr\Replicator\Importer\StatsReporter;
use Queryr\Replicator\ServiceFactory;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
		try {
			$serviceFactory = ServiceFactory::newFromConfig();
		}
		catch ( RuntimeException $ex ) {
			$output->writeln( '<error>Could not instantiate the Replicator app</error>' );
			$output->writeln( '<error>' . $ex->getMessage() . '</error>' );
			return;
		}

		$reporter = new StatsReporter( $this->newReporter( $output ) );

		$importer = $this->newImporter( $serviceFactory, $reporter );

		foreach ( $this->getDumpIterator( $input ) as $entityPage ) {
			$importer->import( $entityPage );
		}

		$this->reportStats( $output, $reporter->getStats() );
	}

	private function getDumpIterator( InputInterface $input ) {
		return $this->newDumpReader( $input->getArgument( 'file' ) )->getIterator();
	}

	private function newDumpReader( $file ) {
		$dumpReaderFactory = new ReaderFactory();
		return $dumpReaderFactory->newDumpReaderForFile( $file );
	}

	private function newImporter( ServiceFactory $serviceFactory, PageImportReporter $reporter ) {
		return new PageImporter(
			$serviceFactory->newDumpStore(),
			$serviceFactory->newEntityDeserializer(),
			$serviceFactory->newQueryStoreWriter(),
			$reporter
		);
	}

	private function newReporter( OutputInterface $output ) {
		return $output->isVerbose() ? new VerboseReporter( $output ) : new SimpleReporter( $output );
	}

	private function reportStats( OutputInterface $output, ImportStats $stats ) {
		$output->writeln( "\n" );

		$output->writeln(
			sprintf(
				'%d entities, %d errors, %d successful, %g error ratio',
				$stats->getEntityCount(),
				$stats->getErrorCount(),
				$stats->getSuccessCount(),
				$stats->getErrorRatio()
			)
		);

		$errors = $stats->getErrorMessages();

		if ( !empty( $errors ) ) {
			$this->reportErrors( $output, $errors );
		}
	}

	private function reportErrors( OutputInterface $output, array $errors ) {
		$output->writeln( "\nErrors:" );

		foreach ( $errors as $errorMessage => $errorCount ) {
			$output->writeln( sprintf(
				"\t* %d times: %s",
				$errorCount,
				$errorMessage
			) );
		}
	}

}
